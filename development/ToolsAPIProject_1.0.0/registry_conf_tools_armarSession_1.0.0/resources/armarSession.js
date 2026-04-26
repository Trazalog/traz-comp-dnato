function armarSession(mc) {
try{
   var log = mc.getServiceLog();
   //Tomo la cookie de sesion de bonita
   var cuki = mc.getProperty("bonitaCookies");
   cuki = cuki != null ? String(cuki) : "";
   // En varios flujos la sesión viaja URL-encoded en path vars (%3D, %3B).
   // Intentamos decode seguro para soportar ambos formatos.
   try {
      if (cuki.indexOf("%") >= 0) {
         cuki = decodeURIComponent(cuki);
      }
   } catch (eDecode) {}
   log.debug("cuki recibida:"+cuki);

   //parseo los parametros
   var xbo = cuki.match(/X-Bonita-API-Token=(?!\;)(.+?);/g);
   if (!xbo || !xbo[0]) {
      throw "No se encontró X-Bonita-API-Token en cookie";
   }
   log.debug("xbo:" + xbo[0]);

   //armo primero el header de X-Bonita-API-Token
   var xbonitaapi = xbo.toString().substr(19);
   log.debug("x-bonita-api-token:" + xbonitaapi.slice(0,-1));

   mc.setProperty('bonitaApiToken',xbonitaapi.slice(0,-1));

   //obtengo el header de session y de bonita.tenant
   var jsession = cuki.match(/JSESSIONID=(?!\;)(.+?);/g);
   if (!jsession || !jsession[0]) {
      throw "No se encontró JSESSIONID en cookie";
   }
   log.debug("jsessionid:" + jsession[0]);

   var btenant = cuki.match(/bonita.tenant=(?!\;)(.+?);/g);
   if (!btenant || !btenant[0]) {
      throw "No se encontró bonita.tenant en cookie";
   }
   log.debug("btenant" + btenant[0]);

   //armo la cookie
   var newcuk = xbo[0]+jsession[0]+btenant[0];
   log.debug('bonitaCookies cuk:'+newcuk);
   mc.setProperty('bonitaCookies',newcuk);

   //usare ambas propiesdades luego para armar headers y cookies
   return true;
}
catch(error1)
{
   log.error("armarSession - ERROR :" + error1);
}
}
