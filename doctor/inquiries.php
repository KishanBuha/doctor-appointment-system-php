<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../includes/db.php";

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit;
}

$doctor_id = $_SESSION['doctor_id'];
$message = "";

if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

// 1. Handle Status Update (Mark as Read)
if (isset($_POST['mark_read'])) {
    $inquiry_id = mysqli_real_escape_string($conn, $_POST['inquiry_id']);
    $query = "UPDATE patient_inquiries SET status = 'Read' WHERE id = '$inquiry_id' AND doctor_id = '$doctor_id'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['flash_message'] = "<div class='alert alert-info alert-dismissible fade show shadow-sm border-0 rounded-4'><i class='bi bi-eye-fill me-2'></i> Message marked as Read.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
    header("Location: inquiries.php");
    exit;
}

// 2. Handle Sending a Direct Reply to Patient
if (isset($_POST['send_reply'])) {
    $inquiry_id = mysqli_real_escape_string($conn, $_POST['inquiry_id']);
    $reply_text = mysqli_real_escape_string($conn, $_POST['reply_message']);

    $query = "UPDATE patient_inquiries SET status = 'Replied', reply_message = '$reply_text' WHERE id = '$inquiry_id' AND doctor_id = '$doctor_id'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['flash_message'] = "<div class='alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4'><i class='bi bi-send-check-fill me-2'></i> Your reply has been sent to the patient.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $_SESSION['flash_message'] = "<div class='alert alert-danger shadow-sm border-0 rounded-4'>Error sending reply.</div>";
    }
    header("Location: inquiries.php");
    exit;
}

// Fetch all received messages
$query = "SELECT pi.*, p.name as patient_name, p.email, p.phone 
          FROM patient_inquiries pi 
          JOIN patients p ON pi.patient_id = p.id 
          WHERE pi.doctor_id = '$doctor_id' 
          ORDER BY pi.created_at DESC";
$result = mysqli_query($conn, $query);

include "../includes/doctor-header.php"; 
?>

<div class="container-fluid pb-5">
    <div class="row mb-4 align-items-end">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Patient Inquiries</h3>
            <p class="text-muted mb-0">Manage and respond to direct messages from your patients.</p>
        </div>
    </div>

    <?php echo $message; ?>

    <div class="row g-4">
        <?php 
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $card_border = "border-start border-4 border-secondary";
                $badge_class = "bg-secondary";
                
                if ($row['status'] == 'Unread') {
                    $card_border = "border-start border-4 border-primary";
                    $badge_class = "bg-primary";
                } elseif ($row['status'] == 'Replied') {
                    $card_border = "border-start border-4 border-success";
                    $badge_class = "bg-success";
                }
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="card <?php echo $card_border; ?> border-top-0 border-bottom-0 border-end-0 shadow-sm rounded-4 h-100 d-flex flex-column">
                <div class="card-body p-4 flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge <?php echo $badge_class; ?> rounded-pill mb-2"><?php echo $row['status']; ?></span>
                        <small class="text-muted fw-bold" style="font-size: 0.75rem;">
                            <i class="bi bi-clock me-1"></i> <?php echo date('M d, h:i A', strtotime($row['created_at'])); ?>
                        </small>
                    </div>
                    
                    <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($row['subject']); ?></h5>
                    <p class="small text-muted mb-3"><i class="bi bi-person-fill text-primary me-1"></i> From: <strong><?php echo htmlspecialchars($row['patient_name']); ?></strong></p>
                    
                    <div class="bg-light p-3 rounded-3 mb-3 small text-dark border">
                        "<?php echo nl2br(htmlspecialchars($row['message'])); ?>"
                    </div>

                    <?php if (!empty($row['reply_message'])) { ?>
                        <div class="bg-success bg-opacity-10 p-3 rounded-3 mb-3 small text-dark border border-success border-opacity-25">
                            <strong class="text-success d-block mb-1"><i class="bi bi-reply-all-fill me-1"></i> Your Reply:</strong>
                            <?php echo nl2br(htmlspecialchars($row['reply_message'])); ?>
                        </div>
                    <?php } ?>

                </div>
                
                <div class="card-footer bg-white border-top p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border rounded-pill dropdown-toggle text-muted fw-bold" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-telephone me-1"></i> Patient Info
                            </button>
                            <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                                <li><a class="dropdown-item small" href="tel:<?php echo $row['phone']; ?>"><i class="bi bi-phone me-2 text-primary"></i><?php echo $row['phone']; ?></a></li>
                                <li><a class="dropdown-item small" href="mailto:<?php echo $row['email']; ?>"><i class="bi bi-envelope me-2 text-danger"></i><?php echo $row['email']; ?></a></li>
                            </ul>
                        </div>

                        <div class="d-flex gap-2">
                            <?php if ($row['status'] == 'Unread') { ?>
                                <form method="POST" class="m-0">
                                    <input type="hidden" name="inquiry_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="mark_read" class="btn btn-sm btn-outline-primary rounded-pill fw-bold px-3">Mark Read</button>
                                </form>
                            <?php } ?>

                            <?php if ($row['status'] != 'Replied') { ?>
                                <button type="button" class="btn btn-sm btn-dark rounded-pill fw-bold px-3" data-bs-toggle="modal" data-bs-target="#replyModal<?php echo $row['id']; ?>">
                                    <i class="bi bi-reply-fill me-1"></i> Reply
                                </button>
                            <?php } ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php if ($row['status'] != 'Replied') { ?>
        <div class="modal fade" id="replyModal<?php echo $row['id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-dark"><i class="bi bi-reply-fill text-primary me-2"></i> Reply to <?php echo htmlspecialchars($row['patient_name']); ?></h5>
                        <button type="button" class="btn-close bg-light rounded-circle p-2 shadow-sm" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body p-4 pt-2">
                            <input type="hidden" name="inquiry_id" value="<?php echo $row['id']; ?>">
                            
                            <div class="bg-light p-3 rounded-3 mb-4 small text-muted border">
                                <strong>Original Message:</strong><br>
                                "<?php echo htmlspecialchars($row['message']); ?>"
                            </div>

                            <div class="form-floating mb-2">
                                <textarea name="reply_message" class="form-control border-primary" placeholder="Type your reply..." style="height: 150px" required></textarea>
                                <label><i class="bi bi-chat-text me-1"></i> Your Reply Text</label>
                            </div>
                            <small class="text-muted">This message will be visible on the patient's portal.</small>
                        </div>
                        <div class="modal-footer bg-light border-top-0 pt-3 pb-3 px-4">
                            <button type="button" class="btn btn-white text-muted fw-bold rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="send_reply" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Send Reply <i class="bi bi-send ms-1"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php 
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-light text-center border-0 shadow-sm rounded-4 py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>You have no patient inquiries at this time.</div></div>';
        }
        ?>
    </div>
</div>

<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>

<?php 
include "../includes/footer.php"; 
ob_end_flush();
?>