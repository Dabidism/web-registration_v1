import sys
import smtplib

def main():
    email = "blaned82@gmail.com"
    
    # PHP calls this script with recipient as argument 1, subject as argument 2, 
    # and pipes the message to standard input. We cannot use input() here as it 
    # will wait indefinitely for user typing when executed by PHP.
    reciever = sys.argv[1] if len(sys.argv) > 1 else ""
    subject = sys.argv[2] if len(sys.argv) > 2 else ""
    message = sys.stdin.read()

    text = f"Subject: {subject}\n\n{message}"

    try:
        server = smtplib.SMTP("smtp.gmail.com", 587)
        server.starttls()

        server.login(email, "exwe pzyg fztd hrnc")

        server.sendmail(email, reciever, text)
        server.quit()
        
        # PHP's send_application_email expects exactly "SUCCESS" to know it worked
        print("SUCCESS")
    except Exception as e:
        print(f"ERROR: {e}")

if __name__ == "__main__":
    main()
