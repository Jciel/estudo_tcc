# estudo_tcc
Estudo projeto TCC


### Login
Connect in URL ws://localhost:8080/login

Return string json
```json
{"error": false, "message": "Login opened"}
```
Envie uma mensagem com os dados de login no padrão
Send a message with login data in pattern
```json
{"user": "username", "passwd": "password"}
```

Return string json if correct
```json
{"error": false, "message": "Valid", "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi..."}
```

If incorrect parameters return string json
```json
{"error": true, "message": "Usuário não existe", "token": null}
```
or
```json
{"error": true, "message": "Senha incorreta", "token": null}
```

### Connecting in channel
With the token connect to the channel URL by passing the token as a
parameter. Ex:

ws://localhost:8080/extruder?token=eyJ0eXAiOiJKV1QiLCJhbGciOi...

If invalid token return string json
```json
{"error": true, "message": "Invalid token", "token": null}
```

If correct, the client is connected in channel
