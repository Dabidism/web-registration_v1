<?php
/**
 * Utility function to send violation email notifications.
 *
 * @param string $ownerEmail The email address of the vehicle owner.
 * @param string $ownerName The name of the vehicle owner.
 * @param string $plateNumber The plate number of the vehicle.
 * @param string $violationType The type/description of the violation.
 * @param string $violationDate The date and time of the violation.
 * @return bool True if email sent successfully, false otherwise.
 */
function sendViolationEmail($ownerEmail, $ownerName, $plateNumber, $violationType, $violationDate) {
    if (empty($ownerEmail)) {
        return false;
    }

    $subject = "Notice of Parking/Gate Pass Violation - ISATU";
    
    $message = "
    <html>
    <head>
      <title>Notice of Violation</title>
      <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background-color: #dc2626; color: white; padding: 10px 20px; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .footer { font-size: 12px; color: #777; margin-top: 20px; text-align: center; }
      </style>
    </head>
    <body>
      <div class='container'>
        <div class='header'>
          <h2>Violation Notice</h2>
        </div>
        <div class='content'>
          <p>Dear {$ownerName},</p>
          <p>This is to inform you that a violation has been recorded for your registered vehicle.</p>
          <ul>
            <li><strong>Plate Number:</strong> {$plateNumber}</li>
            <li><strong>Violation:</strong> {$violationType}</li>
            <li><strong>Date & Time:</strong> {$violationDate}</li>
          </ul>
          <p>Please resolve this violation with the SSEDMMO Office as soon as possible. Unresolved violations may result in the suspension or revocation of your gate pass privileges.</p>
          <p>If you have any questions, please contact the administration.</p>
        </div>
        <div class='footer'>
          <p>This is an automated message. Please do not reply to this email.</p>
        </div>
      </div>
    </body>
    </html>
    ";

    // To send HTML mail, the Content-type header must be set
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=iso-8859-1';

    // Additional headers
    $headers[] = 'From: ISATU Vehicle Admin <no-reply@isatu.edu.ph>';

    // Send email
    return mail($ownerEmail, $subject, $message, implode("\r\n", $headers));
}
?>
