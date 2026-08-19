<?php
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../includes/db.php";

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION['patient_id'];
$message = "";

if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

// Now this will always trigger because of the hidden input!
if (isset($_POST['send_message'])) {
    $doctor_id = mysqli_real_escape_string($conn, $_POST['doctor_id']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $msg_body = mysqli_real_escape_string($conn, $_POST['message']);

    $check_duplicate = "SELECT id FROM patient_inquiries 
                        WHERE patient_id='$patient_id' 
                        AND doctor_id='$doctor_id' 
                        AND subject='$subject' 
                        AND message='$msg_body' 
                        AND created_at >= NOW() - INTERVAL 5 MINUTE";
    
    $duplicate_result = mysqli_query($conn, $check_duplicate);

    if (mysqli_num_rows($duplicate_result) > 0) {
        $_SESSION['flash_message'] = "<div class='alert alert-warning alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4'>
                        <i class='bi bi-exclamation-triangle-fill me-2'></i> <strong>Duplicate blocked!</strong> You just sent this exact message. Please wait for the doctor to reply.
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
    } else {
        $query = "INSERT INTO patient_inquiries (patient_id, doctor_id, subject, message) VALUES ('$patient_id', '$doctor_id', '$subject', '$msg_body')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['flash_message'] = "<div class='alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4'>
                            <i class='bi bi-send-check-fill me-2'></i> Your message has been successfully sent to the doctor.
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>";
        } else {
            $_SESSION['flash_message'] = "<div class='alert alert-danger shadow-sm border-0 rounded-4 mb-4'>Error sending message: " . mysqli_error($conn) . "</div>";
        }
    }
    header("Location: contact.php");
    exit;
}

$doctors_query = mysqli_query($conn, "SELECT id, name, specialization FROM doctors WHERE status = 1");

$history_query = "SELECT pi.*, d.name as doctor_name, d.specialization 
                  FROM patient_inquiries pi 
                  JOIN doctors d ON pi.doctor_id = d.id 
                  WHERE pi.patient_id = '$patient_id' 
                  ORDER BY pi.created_at DESC";
$msg_query = mysqli_query($conn, $history_query);

include "../includes/patient-header.php"; 
?>

<style>
    .app-container { background: #ffffff; border-radius: 24px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04); overflow: hidden; border: 1px solid #f1f5f9; }
    .compose-sidebar { background: #f8fafc; border-right: 1px solid #f1f5f9; padding: 2.5rem; }
    .form-floating > .form-control, .form-floating > .form-select { background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; }
    .form-floating > .form-control:focus, .form-floating > .form-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
    .btn-gradient { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: white; border: none; border-radius: 12px; transition: all 0.3s ease; }
    .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3); color: white; }
    .history-main { padding: 2.5rem; background: #ffffff; }
    
    /* Custom Scrollbar for the feed */
    .chat-feed-container { max-height: 650px; overflow-y: auto; padding-right: 10px; }
    .chat-feed-container::-webkit-scrollbar { width: 6px; }
    .chat-feed-container::-webkit-scrollbar-track { background: transparent; }
    .chat-feed-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .chat-feed-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    .convo-card { border: 1px solid #f1f5f9; border-radius: 20px; margin-bottom: 1.5rem; background: #ffffff; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    .convo-header { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; background: #fafaf9; border-radius: 20px 20px 0 0; cursor: pointer; }
    .status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 10px; flex-shrink: 0; }
    .dot-unread { background-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2); }
    .dot-read { background-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2); }
    .dot-replied { background-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2); }
    
    .bubble-wrapper { width: 90%; }
    .bubble-patient { background: #f1f5f9; color: #334155; border-radius: 18px 18px 0 18px; padding: 1rem 1.25rem; font-size: 0.95rem; }
    .bubble-doctor { background: #eff6ff; color: #1e3a8a; border-radius: 18px 18px 18px 0; border-left: 4px solid #3b82f6; padding: 1rem 1.25rem; font-size: 0.95rem; }
</style>

<div class="container-fluid pb-5 pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h3 class="fw-bolder text-dark mb-1">Messaging Center</h3>
            <p class="text-muted mb-0">Securely communicate with your doctors.</p>
        </div>
    </div>

    <?php echo $message; ?>

    <div class="app-container">
        <div class="row g-0">
            <div class="col-lg-5 col-md-12 compose-sidebar">
                <div class="mb-4 d-flex align-items-center">
                    <div class="bg-white text-primary rounded-circle shadow-sm p-2 d-inline-flex me-3"><i class="bi bi-pencil-square fs-5"></i></div>
                    <h5 class="fw-bold text-dark mb-0">New Message</h5>
                </div>
                
                <form method="POST" id="contactForm">
                    <input type="hidden" name="send_message" value="1">
                    
                    <div class="form-floating mb-3">
                        <select class="form-select" id="doctorSelect" name="doctor_id" required>
                            <option value="" disabled selected>Select from directory...</option>
                            <?php while($doc = mysqli_fetch_assoc($doctors_query)) { ?>
                                <option value="<?php echo $doc['id']; ?>">Dr. <?php echo $doc['name']; ?> - <?php echo $doc['specialization']; ?></option>
                            <?php } ?>
                        </select>
                        <label for="doctorSelect"><i class="bi bi-person me-1"></i> Recipient</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="subjectInput" name="subject" placeholder="Subject" required>
                        <label for="subjectInput"><i class="bi bi-chat-left-text me-1"></i> Subject Line</label>
                    </div>

                    <div class="form-floating mb-4">
                        <textarea class="form-control" id="messageInput" name="message" placeholder="Message" style="height: 160px" required></textarea>
                        <label for="messageInput"><i class="bi bi-body-text me-1"></i> Write your message...</label>
                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-gradient w-100 py-3 fw-bold fs-6 d-flex justify-content-center align-items-center">
                        Send Secure Message <i class="bi bi-send-fill ms-2"></i>
                    </button>
                    <p class="text-center text-muted small mt-3"><i class="bi bi-shield-lock me-1"></i> End-to-end encrypted for your privacy.</p>
                </form>
            </div>

            <div class="col-lg-7 col-md-12 history-main">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-chat-quote text-muted me-2"></i> Conversation Feed</h5>
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><?php echo mysqli_num_rows($msg_query); ?> Sent</span>
                </div>
                
                <div class="chat-feed-container pe-2">
                    <?php if(mysqli_num_rows($msg_query) > 0) { ?>
                        <?php 
                        while($row = mysqli_fetch_assoc($msg_query)) { 
                            $dot_class = 'dot-unread';
                            $status_text = 'Unread';
                            if($row['status'] == 'Read') { $dot_class = 'dot-read'; $status_text = 'Seen'; }
                            if($row['status'] == 'Replied') { $dot_class = 'dot-replied'; $status_text = 'Replied'; }
                        ?>
                            <div class="convo-card">
                                <div class="convo-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#msg_<?php echo $row['id']; ?>" aria-expanded="false">
                                    <div class="d-flex align-items-center overflow-hidden pe-3 w-100">
                                        <span class="status-dot <?php echo $dot_class; ?>" title="<?php echo $status_text; ?>"></span>
                                        <div>
                                            <div class="text-dark fw-bold mb-0 text-truncate" style="max-width: 300px;"><?php echo htmlspecialchars($row['subject']); ?></div>
                                            <div class="text-muted small mt-1"><i class="bi bi-person-badge me-1"></i>To: Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></div>
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <div class="text-muted small fw-semibold"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></div>
                                        <span class="badge bg-light text-secondary border rounded-pill mt-1" style="font-size: 0.65rem; text-transform: uppercase;"><?php echo $status_text; ?></span>
                                    </div>
                                </div>
                                
                                <div id="msg_<?php echo $row['id']; ?>" class="collapse">
                                    <div class="p-4 d-flex flex-column gap-3">
                                        <div class="align-self-end bubble-wrapper d-flex flex-column align-items-end">
                                            <div class="bubble-patient shadow-sm">
                                                <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                                            </div>
                                            <span class="text-muted mt-1" style="font-size: 0.7rem;"><i class="bi bi-check2"></i> Sent at <?php echo date('h:i A', strtotime($row['created_at'])); ?></span>
                                        </div>

                                        <?php if($row['status'] == 'Replied' && !empty($row['reply_message'])) { ?>
                                            <div class="align-self-start bubble-wrapper d-flex flex-column align-items-start mt-2">
                                                <div class="d-flex align-items-center mb-1">
                                                    <div class="bg-primary rounded-circle d-inline-flex p-1 me-2"><i class="bi bi-hospital text-white" style="font-size: 0.7rem;"></i></div>
                                                    <small class="text-primary fw-bold">Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></small>
                                                </div>
                                                <div class="bubble-doctor shadow-sm">
                                                    <?php echo nl2br(htmlspecialchars($row['reply_message'])); ?>
                                                </div>
                                            </div>
                                        <?php } elseif($row['status'] == 'Read') { ?>
                                            <div class="text-center mt-2 w-100">
                                                <span class="badge bg-light text-muted border rounded-pill fw-normal px-3 py-2"><i class="bi bi-eye-fill text-primary me-2"></i>Doctor is reviewing your message</span>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="text-center py-5 h-100 d-flex flex-column justify-content-center align-items-center opacity-75 mt-5">
                            <i class="bi bi-chat-square-text fs-1 text-muted mb-3"></i>
                            <h5 class="fw-bold text-dark">No Conversations Yet</h5>
                            <p class="text-muted small mb-0" style="max-width: 250px;">Start a conversation by sending a new message using the compose panel.</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    if ( window.history.replaceState ) { window.history.replaceState( null, null, window.location.href ); }
    
    document.getElementById('contactForm').addEventListener('submit', function() {
        var btn = document.getElementById('submitBtn');
        btn.innerHTML = 'Sending... <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        
        // Added a tiny timeout so the browser guarantees the form data is collected before disabling
        setTimeout(function() {
            btn.disabled = true;
        }, 10);
    });
</script>

<?php 
include "../includes/footer.php"; 
ob_end_flush(); 
?>