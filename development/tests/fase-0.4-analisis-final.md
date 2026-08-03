# Análisis Final - Fase 0.4

## Problema Identificado

El error `WstxEOFException: Unexpected EOF in prolog` ocurre cuando WSO2 intenta parsear la respuesta del DataService.

## Comparación con Crear Empresa (Funciona)

**Crear Empresa (líneas 34-56)**:
```xml
<property name="messageType" value="application/json" scope="axis2"/>
<header name="Accept" scope="transport" value="application/json"/>
<call>
    <endpoint>
        <http method="POST" uri-template="{uri.var.crear_empresa_url}"/>
    </endpoint>
</call>
<filter source="get-property('axis2', 'HTTP_SC')" regex="2[0-9][0-9]">
    <then/>
    <else>
        <property name="ERROR_MESSAGE" expression="json-eval($)" type="STRING"/>
        <sequence key="toolsFault"/>
    </else>
</filter>
<property name="empr_id" expression="json-eval($.GeneratedKeys.Entry[0].ID)"/>
```

**Crear Usuario (actual, NO funciona)**:
```xml
<property name="messageType" value="application/json" scope="axis2"/>
<header name="Accept" scope="transport" value="application/json"/>
<call>
    <endpoint>
        <http method="POST" uri-template="{uri.var.crear_usuario_url}"/>
    </endpoint>
</call>
<filter source="get-property('axis2', 'HTTP_SC')" regex="2[0-9][0-9]">
    <then/>
    <else>
        <property name="ERROR_MESSAGE" value="Error HTTP al crear usuario" type="STRING"/>
        <sequence key="toolsFault"/>
    </else>
</filter>
<property name="usr_id" expression="json-eval($.GeneratedKeys.Entry[0].ID)"/>
```

## Diferencias Encontradas

1. ✅ **Headers**: Ambos tienen `Accept` y `messageType` antes del call
2. ✅ **Estructura**: Ambos tienen la misma estructura
3. ❌ **ERROR_MESSAGE en else**: Crear empresa usa `json-eval($)`, crear usuario usa valor fijo (ya corregido)

## Hipótesis

El problema podría estar en que:
1. El DataService no está devolviendo JSON cuando se llama desde la API (aunque funciona directamente)
2. La respuesta está vacía o mal formada
3. El `json-eval($.GeneratedKeys.Entry[0].ID)` está fallando porque la respuesta no tiene ese formato

## Próxima Investigación

Necesito verificar:
1. Si el HTTP_SC es realmente 2xx
2. Si la respuesta del DataService está llegando correctamente
3. Si el formato de la respuesta es el esperado

