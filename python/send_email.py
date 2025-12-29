import smtplib
import sys
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

def send_email(recipient_email, subject, body):
    sender_email = "g8pass.isatu@gmail.com"
    app_password = "oeezikvcmcrujwkm"
    
    try:
        message = MIMEMultipart()
        message["From"] = sender_email
        message["To"] = recipient_email
        message["Subject"] = subject
        message.attach(MIMEText(body, "plain"))
        
        with smtplib.SMTP("smtp.gmail.com", 587) as server:
            server.starttls()
            server.login(sender_email, app_password)
            server.send_message(message)
            return True
    except Exception as e:
        return False

if __name__ == "__main__":
    if len(sys.argv) != 3:
        sys.exit(1)
    
    recipient = sys.argv[1]
    subject = sys.argv[2]
    body = sys.stdin.read()
    
    success = send_email(recipient, subject, body)
    print("SUCCESS" if success else "FAILED")