openssl genrsa -out current/private.pem 2048
openssl rsa -in current/private.pem -outform PEM -pubout -out current/public.pem
