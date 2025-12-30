# 🔧 Corrección del Problema de Payload Vacío

## Problema Identificado

Después del `call` para verificar duplicados, el payload original se pierde y se reemplaza con la respuesta del Data Service. Cuando el `payloadFactory` intenta extraer valores con `json-eval($.usuario.*)`, el payload ya no contiene esos datos.

## Solución Implementada

1. **Guardar todas las propiedades ANTES del `call`** (líneas 231-241)
2. **Usar `get-property()` en lugar de `json-eval()`** en el `payloadFactory` (líneas 271-283)

## Cambios Realizados

### Antes:
```xml
<payloadFactory>
   <args>
      <arg evaluator="json" expression="$.usuario.firstname"/>
      ...
   </args>
</payloadFactory>
```

### Después:
```xml
<!-- Guardar propiedades antes del call -->
<property name="usr_firstname" expression="json-eval($.usuario.firstname)"/>
...

<!-- Usar propiedades guardadas después del call -->
<payloadFactory>
   <args>
      <arg evaluator="xml" expression="get-property('usr_firstname')"/>
      ...
   </args>
</payloadFactory>
```

## Próximo Paso

**IMPORTANTE:** El API necesita ser **redesplegado en WSO2** para que los cambios surtan efecto.

1. Copiar el archivo actualizado: `toolsCOREAPI.xml` → WSO2
2. Reiniciar WSO2 o esperar a que se redespliegue automáticamente
3. Probar nuevamente

## Verificación

Después de redesplegar, el log debería mostrar:
- `donde 1.5 = propiedades guardadas` con los valores correctos
- `donde 2 = pre post crear_usuario` con payload con valores llenos

