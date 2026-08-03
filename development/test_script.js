// Test script para verificar sintaxis
var password = "password123";
var md5Hash = Packages.org.apache.commons.codec.digest.DigestUtils.md5Hex(password);
print("MD5 Hash: " + md5Hash);
