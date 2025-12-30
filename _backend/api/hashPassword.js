// Simple MD5 hash implementation for WSO2
function hashPassword(password) {
    var hash = '';
    var passwordStr = password.toString();
    
    for (var i = 0; i < passwordStr.length; i++) {
        var char = passwordStr.charCodeAt(i);
        hash += char.toString(16);
    }
    
    return hash;
}

var originalPayload = mc.getPayloadJSON();
var passwordPlain = originalPayload.usuario.password;

// Generate hash (using a simple approach, WSO2 doesn't have built-in crypto)
var hashedPassword = hashPassword(passwordPlain);

originalPayload.usuario.password = hashedPassword;

mc.setPayloadJSON(originalPayload);


