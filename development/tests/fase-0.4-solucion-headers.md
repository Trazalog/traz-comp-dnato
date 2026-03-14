# Solución Propuesta - Fase 0.4 Headers JSON

## Problema Identificado

El error `WstxEOFException: Unexpected EOF in prolog` ocurre cuando WSO2 intenta parsear la respuesta del DataService como XML en lugar de JSON.

## Solución Aplicada

Se establecieron los headers `Accept` y `Content-Type` a `application/json` **ANTES** de la llamada al DataService:

```xml
<header name="Accept" scope="transport" value="application/json"/>
<header name="Content-Type" scope="transport" value="application/json"/>
<call>
    <endpoint>
        <http method="POST" uri-template="{uri.var.crear_usuario_url}"/>
    </endpoint>
</call>
<property name="messageType" value="application/json" scope="axis2"/>
```

## Estado Actual

- ✅ Headers establecidos correctamente antes del `call`
- ✅ `messageType` establecido después del `call`
- ⚠️ El error persiste al intentar parsear la respuesta

## Próxima Investigación

El problema podría estar en que:
1. El DataService no está devolviendo JSON cuando se llama desde la API (aunque funciona directamente)
2. La respuesta está vacía o mal formada
3. Necesita configuración adicional en el endpoint HTTP

## Referencias

- WSO2 Documentation: Headers deben establecerse antes del `call` mediator
- WSO2 Community: Para JSON, siempre establecer `Accept: application/json` y `Content-Type: application/json`

