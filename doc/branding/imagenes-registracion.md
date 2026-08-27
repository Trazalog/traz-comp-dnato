# Imágenes del flujo de registración — generación con IA para producción

## Objetivo

Es el instructivo para **reemplazar las imágenes provisorias del flujo de registración por imágenes definitivas de producción**, generadas con IA. Dice qué herramienta usar y por qué, qué proporción y peso necesita cada imagen (medido contra el CSS real de cada vista), y trae los **prompts listos para copiar y pegar**, uno por pantalla. Se ejecuta desde el navegador: no hace falta tocar código para generar, sólo para instalar el resultado (§9).

**No cubre**: el flujo funcional de la registración (eso está en `doc/PROCESO_REGISTRACION.md`), ni el diseño de las pantallas en sí, ni el logo de marca — `logotzl.png` y `toolsgrey.png` son activos de marca existentes y **no se tocan**.

---

## 0. Estado — dónde quedó esto (leer primero al retomar)

> **Frente PAUSADO el 2026-08-27** a pedido del PM, para retomar más adelante. No está cerrado.

| Imagen | Estado |
|---|---|
| `toolsregister.png` | ✅ **Resuelta.** Sirve la generada con Qwen-Image (pit andino, dos operarios de espaldas), 1024×1024. Falta el retoque menor de §8 y corregir la mano/tablet del operario derecho |
| `toolschangepass.png` | ⏳ Falta generar — prompt v2 en §6.2 |
| `toolsform.png` | ⏳ Falta generar — prompt v2 en §6.3 (reencuadrado a bodegón sin personas) |
| `toolscreaempr.png` | ⏳ Falta generar — prompt v2 en §6.4. **Es la que hoy da 404** |
| `toolsbienvenida.png` | ⏳ Falta generar — prompt v2 en §6.5 (reencuadrado a una sola persona) |

**Material de la tanda 1** (5 de FLUX schnell + 1 de Qwen): `/mnt/win/dev/Trazalog/dnato/`. El veredicto imagen por imagen está en §3-bis; de las de FLUX sólo se rescata la del depósito.

**Decisión de fondo pendiente, que conviene tomar antes de seguir generando:** si "que no se note que es IA" es requisito duro para producción, la IA fotorrealista de **personas** todavía no llega del todo — la de paisajes e infraestructura sí. Dos caminos alternativos sobre la mesa, ninguno descartado:

1. **Híbrido**: banco de fotos con licencia comercial (Pexels, Unsplash) para las escenas con gente, IA para los ambientes.
2. **Fotos propias en un cliente minero real**: media jornada de producción da material auténtico, irrepetible y, de paso, un caso de éxito. Ninguna IA da eso.

---

## 1. Estado actual — tres cosas que conviene saber antes de empezar

Verificado sobre `develop-v3` el 2026-08-21:

**a) Hay una sola imagen, repetida cuatro veces.** `toolsregister.png`, `toolschangepass.png`, `toolsform.png` y `toolsbienvenida.png` son **el mismo archivo byte a byte** (MD5 `1bee3085a463b74941ea08977498ed6f`, 1024×1024, 1,6 MB cada copia). No son cuatro imágenes provisorias: es una sola, copiada.

**b) La imagen del alta de empresa no existe.** `REGISTER_IMG_CREAR_EMPRESA` apunta a `public/img/toolscreaempr.png` y **ese archivo no está en el repo** → hoy la pantalla `register/crearEmpresa` muestra una imagen rota. Es un `<img>` sin fallback (a diferencia de `formulario_page.php`, que sí tiene uno). Se resuelve solo al subir la imagen nueva con ese nombre exacto.

**c) Pesan demasiado para lo que se ven.** 1,6 MB para renderizarse a ~320 px de ancho. En la pantalla de registro —que es la primera impresión de un visitante que quizá esté con conexión de mina— eso es medio segundo regalado. Ver §8.

---

## 2. Qué IA usar

### 2.1 El criterio que decide

Salir a producción es **uso comercial**. La mayoría de los planes gratuitos famosos no lo permiten, o te marcan la imagen. Eso descarta a casi todos antes de mirar la calidad:

| Herramienta | Gratis | Watermark | ¿Uso comercial en el plan gratuito? | Veredicto |
|---|---|---|---|---|
| Google Gemini | ~15 img/día | **Sí, visible (✦)** | Sí | ❌ el watermark visible lo mata |
| Microsoft Copilot / Bing Image Creator | Sí | No | **Ambiguo** — el Services Agreement lo limita a uso personal salvo planes M365 comerciales | ❌ riesgo legal |
| Adobe Firefly (plan gratuito) | ~25 créditos/mes | No | **No** — el gratuito es para evaluación y uso personal; la indemnización sólo viene con los planes pagos | ❌ gratis no sirve (pero ver §2.3) |
| Ideogram (plan gratuito) | ~10 créditos/día | No | Contradictorio según la fuente, y **las generaciones son públicas por defecto** | ⚠️ no me apoyaría en él |
| **FLUX.1 [schnell]** / **FLUX.2 [klein]** | Sí | No | **Sí — Apache 2.0, explícito** | ✅ **recomendado** |
| Qwen-Image | Sí | No | Sí — Apache 2.0 | ✅ alternativa |

### 2.2 Recomendación: Qwen-Image (validado en la práctica)

> **Actualizado el 2026-08-21, después de la primera tanda real.** Este documento recomendaba originalmente FLUX.1 [schnell]. Se generaron las cinco con schnell y una con Qwen-Image, y **Qwen ganó por diferencia clara**. La recomendación cambia. Ver §3-bis para el análisis.

**Qwen-Image**, licencia **Apache 2.0** — las mismas garantías legales que FLUX (uso comercial explícito, sin atribución, sin regalías, sin watermark), pero bastante mejor resultado en este tipo de escena: paisaje andino, gente de espaldas, profundidad real, colores contenidos.

**El problema de FLUX.1 [schnell] no es FLUX, es *schnell*.** Es la variante **destilada de 4 pasos**, hecha para velocidad, no para calidad: es la más débil de la familia. Las alternativas buenas de FLUX son de pago o de licencia no comercial, salvo **FLUX.2 [klein]** (también Apache 2.0), que vale la pena probar si Qwen no te convence en alguna escena puntual.

**Dónde usarlos sin instalar nada:** ambos están en Hugging Face con cuenta gratuita, desde los Spaces oficiales de cada modelo.

> ⚠️ **Ojo con esto:** la licencia Apache 2.0 cubre **el modelo**. Si lo corrés en un sitio de terceros ("generá gratis online", Spaces de usuarios random), a la licencia del modelo **se le suman los términos de ese sitio**, que pueden reclamar derechos sobre lo que generás o reservar el uso comercial. Usá el Space oficial, o corré el modelo localmente. Si el sitio no te muestra sus términos, no lo uses para material de producción.

### 2.3 La opción que no es gratis, y por qué te la menciono igual

**Adobe Firefly Standard, USD 9,99/mes.** Está entrenado exclusivamente con Adobe Stock licenciado, dominio público y contenido con licencia abierta —nada raspado de la web— y los planes pagos incluyen **indemnización de Adobe** ante reclamos de propiedad intelectual sobre lo que generes.

Lo traigo porque tu cliente objetivo son mineras: compradores corporativos con área legal. Diez dólares por mes a cambio de que un tercero se haga cargo del riesgo de PI en el material público de tu producto puede ser la compra más barata del proyecto. **Vos decidís** — FLUX cumple con lo que pediste y es lo que este documento asume; esto es sólo para que la decisión sea informada.

---

## 3. Cinco reglas antes de escribir un prompt

Este caso de uso tiene trampas propias. Las cinco salen de qué le estás vendiendo a quién.

**1. El EPP mal representado es un autogol.** Le vendés trazabilidad y control operativo a empresas mineras. Si la foto de tu home muestra a alguien sin antiparras en una zona donde corresponden, o un casco mal calzado, o alguien en altura sin arnés, el primer cliente que la mire lo va a ver. Los prompts de §6 especifican el EPP explícitamente, **y aun así alguien que conozca la operación real tiene que aprobar cada imagen antes de subirla.** La IA no sabe de normativa argentina.

**2. Manos y texto siguen fallando.** Los modelos actuales mejoraron mucho, pero las manos en primer plano y el texto pequeño siguen saliendo mal. Los prompts están escritos para evitar los dos: manos en gestos simples o fuera del plano principal, y ninguna pantalla legible.

**3. Nunca le pidas que escriba "Trazalog" ni que dibuje tu interfaz.** Va a salir un garabato que parece texto. Si querés mostrar el producto en uso, la técnica correcta es: **generar la foto con el dispositivo apagado, en ángulo o con la pantalla desenfocada, y después componer un screenshot real encima** con cualquier editor. Los prompts de §6 están escritos para dejar ese hueco preparado.

**4. Caras: mejor semi-cubiertas.** Reduce el riesgo de parecido con una persona real y, de paso, queda mejor: casco y antiparras, perfil, tres cuartos, o de espaldas mirando la operación. Nada de primer plano frontal sonriendo a cámara — eso además grita "banco de imágenes".

**5. Es Cuyo, no Arizona.** Alta montaña andina árida: roca ocre y gris, sin vegetación densa, cielo profundo y limpio, horizonte de cordillera. Si no lo pedís explícitamente, el modelo te devuelve el desierto americano o una mina de carbón europea, y se nota.

---

## 3-bis. Por qué la primera tanda se nota que es IA

Relevado el 2026-08-21 sobre las 6 imágenes generadas (5 con FLUX.1 [schnell], 1 con Qwen-Image). El diagnóstico es de tres capas, y **las tres se arreglan**.

### a) El modelo

`schnell` es la variante destilada de 4 pasos. Es la más rápida y la más floja de la familia FLUX. Qwen-Image, con la misma licencia Apache 2.0, dio en el primer intento un resultado mejor que las cinco de schnell. **Cambiar de modelo es la mitad de la mejora, sin tocar nada más.**

### b) Los prompts de la v1 de este documento tenían tres fallas

1. **La biblia visual estaba en una tabla y no dentro de cada prompt.** Consecuencia directa: donde el prompt no repetía "latinoamericanas", el modelo devolvió personas del norte de Europa. En los prompts v2 la biblia va **escrita adentro de cada uno**.
2. **No se pedía imperfección.** Una foto parece foto por sus defectos: grano, polvo, ropa gastada, luz que no favorece, encuadre no perfecto. Los prompts v1 pedían lo contrario ("calm and composed", "muted earth palette") y el modelo devolvió el look de catálogo: naranjas fluo saturados, ropa recién comprada, cielo con degradado perfecto y bokeh de retrato de celular.
3. **Nada frenaba el desenfoque de fondo.** El bokeh exagerado es una de las marcas más delatoras de la IA. Los v2 piden `deep focus, f/8`.

### c) No hubo post-proceso

Ninguna imagen generada sale lista. Grano + desaturación leve + bajar el contraste hace la otra mitad del trabajo. Ver §8.

### El patrón que importa: qué encuadres funcionan

Mirando las seis juntas aparece una regla que vale más que cualquier ajuste de prompt:

| Funciona ✅ | Falla ❌ |
|---|---|
| Personas **de espaldas** o de perfil lejano | Caras frontales o en primer plano |
| Una sola persona en el encuadre | Dos personas interactuando entre sí |
| Manos vacías, o fuera del plano principal | **Manos sosteniendo o intercambiando objetos** |
| Paisaje, infraestructura, objetos | Pantallas, carteles, papeles con texto |
| Profundidad real (perspectiva, capas) | Fondo desenfocado con bokeh fuerte |

Todos los defectos graves de la tanda 1 caen en la columna derecha: la oficina con dos ingenieros sobre un plano (manos deformes + texto garabateado en el plano), la entrega de la caja (cuatro manos y una caja con geometría imposible), la del celular (dedos mal). Las dos mejores —la de Qwen y la del depósito— tienen a la persona **de espaldas y sola**.

**Conclusión operativa: no alcanza con mejorar el prompt, hay que reencuadrar el concepto.** Los prompts v2 de §6 bajan la cantidad de personas y sacan las manos del plano principal.

### Veredicto imagen por imagen (tanda 1)

| Imagen | Veredicto | Por qué |
|---|---|---|
| **Qwen — pit andino con dos operarios de espaldas** | ✅ **Se usa**, con retoque menor | Composición y escala correctas, colores contenidos, cordillera creíble. A corregir: la mano y la tablet del operario derecho, y su cara semi-visible. Se resuelve recortando un poco o con inpainting |
| FLUX — depósito con racks en perspectiva | ✅ Rescatable | La mejor de schnell: perspectiva de un punto muy legible, persona sola y de espaldas. A corregir: los racks del fondo se degradan en ruido geométrico, cielo demasiado liso, luz de mediodía (no la media mañana pedida), guante rojo fuera de paleta |
| FLUX — pit con operario al borde | ❌ Descartar | **La sombra del operario está desprendida del cuerpo**, proyectada lejos dentro del pit. Además naranja fluo irreal y —grave para tu caso— **el operario está parado al borde de un talud sin protección** |
| FLUX — mujer con celular en el acceso | ⚠️ Sólo con retoque fuerte | El sujeto y el perfil están bien, pero los dedos que sostienen el teléfono están mal, las bandas reflectivas no cierran como prenda, hay texto fantasma en la manga y en el cartel del fondo, y es un celular común, no un equipo rugerizado |
| FLUX — oficina con dos ingenieros y un plano | ❌ Descartar | Manos deformes en los dos, texto garabateado en el plano, dos cascos idénticos (uno gigante en primer plano), personas del norte de Europa, luz plana y muerta, y la oficina no parece un contenedor de obra |
| FLUX — entrega de caja junto a la camioneta | ❌ Descartar | Cuatro manos y una caja con geometría imposible, bokeh de retrato de celular, sobresaturación dorada tipo filtro, y sonrisa de banco de imágenes — justo lo que había que evitar |

---

## 4. Biblia visual — pegar en todos los prompts

El error más común al generar imágenes de a una es que terminen pareciendo cinco sitios distintos. Se evita fijando de antemano hora del día, paleta, lente y vestuario, e incluyéndolos en **todos** los prompts.

| Parámetro | Valor fijo para las cinco |
|---|---|
| Región | Cordillera de los Andes, Cuyo (San Juan / Mendoza) — montaña árida, roca ocre y gris |
| Hora | Media mañana, luz cálida y rasante, sombras largas y suaves |
| Clima | Despejado, aire limpio de altura, cielo azul profundo |
| Lente | 35 mm para ambientes, 50 mm para personas; f/4; fotografía documental corporativa |
| Paleta | Tierra ocre y gris piedra + **naranja de alta visibilidad** y **azul marino** en la ropa de trabajo |
| Personas | Latinoamericanas, 30-50 años, mezcla de géneros, concentradas y competentes — nunca sonriendo a cámara |
| EPP | Casco, antiparras, ropa de alta visibilidad con bandas reflectivas, guantes, botas de seguridad |
| Prohibido | Texto legible, logos, pantallas nítidas, primeros planos de manos, poses de banco de imágenes |
| **Textura de foto real** | Grano de película fino, colores desaturados, contraste plano, **foco profundo a f/8** (nunca bokeh fuerte), ropa gastada y con polvo, cascos rayados, piel con textura visible, sin retoque |
| **Encuadre** | Personas de espaldas o de perfil, **una sola por imagen**, manos vacías o fuera del plano principal (§3-bis) |

El naranja y el azul marino no son decorativos: **el azul marino conversa con el `#3498db` de los botones de la interfaz**, así que las fotos y la UI van a verse de la misma familia.

> **Nota técnica sobre prompts negativos.** Qwen-Image **sí** los acepta y conviene usarlos (§7). FLUX.1 [schnell] **no**: corre destilado con *guidance* fija y los ignora, así que ahí todo lo que no querés hay que expresarlo en positivo dentro del prompt ("pantalla apagada" en vez de "sin texto en pantalla"). Los prompts de §6 están escritos para funcionar en los dos casos.

---

## 5. Qué necesita cada imagen — medido contra el CSS real

No todas se usan igual, y esto cambia el encuadre. Verificado leyendo cada vista:

| Constante | Archivo | Vista | Cómo se renderiza | Proporción a generar |
|---|---|---|---|---|
| `REGISTER_IMG_BACKGROUND` | `toolsregister.png` | `register.php` | Fondo del panel derecho: **60 % del ancho × 100 % del alto**, `background-size: cover`, `center`. Sin texto encima (el formulario vive en el 40 % izquierdo, sobre gris sólido) | **1:1, 1536×1536** — ver nota abajo |
| `REGISTER_IMG_COMPLETE_PASSWORD` | `toolschangepass.png` | `complete_password.php` | Panel derecho, ~320 px de ancho, esquinas redondeadas y sombra | **1:1, 1024×1024** |
| `REGISTER_IMG_FORMULARIO` | `toolsform.png` | `formulario_page.php` | Ídem | **1:1, 1024×1024** |
| `REGISTER_IMG_CREAR_EMPRESA` | `toolscreaempr.png` | `crear_empresa_page.php` | Ídem — **hoy da 404** | **1:1, 1024×1024** |
| `REGISTER_IMG_BIENVENIDA` | `toolsbienvenida.png` | `bienvenida_page.php` | Ídem | **1:1, 1024×1024** |

> **Por qué la del registro también es 1:1 y no panorámica.** Ocupa el 60 % del ancho a pantalla completa: en 1920×1080 son 1152×1080 px, o sea **casi cuadrada** (≈1,07:1); en 1366×768, 820×768 (la misma proporción). Como es `cover`, el recorte cambia con cada resolución. Generala **cuadrada, con el motivo centrado y aire alrededor**, para que sobreviva al recorte. Va más grande (1536) porque se muestra mucho más grande que las otras cuatro.
>
> FLUX.1 [schnell] rinde mejor cerca de 1 megapíxel. Para la de 1536×1536, generá a 1024×1024 y **escalá después** con un upscaler (los propios Spaces de Hugging Face tienen; también sirve `waifu2x`, Upscayl, o cualquier editor). Sale mejor que pedirle 1536 de una.

---

## 6. Los prompts (v2)

> **v2, 2026-08-21.** Reescritos después de la tanda 1 (§3-bis). Tres cambios: la biblia visual va **dentro** de cada prompt (no en una tabla aparte), se pide **imperfección fotográfica** explícita, y **se reencuadró el concepto** de las dos escenas que fallaron para sacar manos y caras del plano principal.

En inglés, en prosa continua. **Copiar y pegar completo.** Generá **4 variantes de cada uno** (misma prompt, distinta semilla) y elegí — es gratis y la diferencia entre la primera y la mejor de cuatro es grande.

Si usás Qwen-Image, sumale además el prompt negativo de §7.

---

### 6.1 `toolsregister.png` — pantalla de registro

**Ya está resuelta.** La imagen de Qwen de la tanda 1 sirve (§3-bis). Este prompt queda por si querés más variantes o tenés que regenerarla.

**Qué tiene que transmitir:** escala y seriedad. Es lo primero que ve un visitante, junto al texto "Regístrese Gratis".

```
Editorial photograph for a mining industry trade magazine. An open-pit copper
mine high in the arid Andes of San Juan, Argentina. Wide ochre and grey rock
benches step down toward the centre of the frame; haul trucks far below are tiny,
giving scale; a hazy cordillera ridgeline sits on the horizon under a pale blue
sky with thin high cloud. Two Argentinian mine workers in their forties, seen
from behind and slightly above, stand small on a bench edge looking out over the
pit. Their navy blue coveralls are dusty and faded, the orange reflective bands
worn and scuffed, hard hats scratched, boots caked in pale dust. Late morning,
hard directional sunlight, shadows falling consistently to the left. Shot on a
35mm lens at f/8, deep focus, everything sharp from foreground to horizon, fine
35mm film grain, muted desaturated colours, slightly flat contrast, unposed
candid documentary photojournalism, no retouching.
```

---

### 6.2 `toolschangepass.png` — activación de cuenta y contraseña

**Qué tiene que transmitir:** acceso autorizado, identidad verificada. **Reencuadrado**: la persona ahora está de espaldas y sin dispositivo en la mano — el fallo de la v1 fueron los dedos sobre el teléfono.

```
Editorial photograph for a mining industry trade magazine. A vehicle access
control point at a mining site in the arid Andes of San Juan, Argentina. A weath-
ered steel boom gate crosses the frame; a small modular guard cabin with dusty
windows stands to the right; pale ochre mountain slopes rise behind. An Argentin-
ian site supervisor in her forties, seen from behind at medium distance, stands
at the gate facing the cabin, hands at her sides, wearing dusty navy blue
coveralls with worn orange reflective bands, a scratched white hard hat and a
ponytail. Late morning, hard directional sunlight, long shadows falling
consistently to the left, fine wind-blown dust in the air. Shot on a 35mm lens at
f/8, deep focus, both the supervisor and the cabin sharp, fine 35mm film grain,
muted desaturated colours, slightly flat contrast, unposed candid documentary
photojournalism, no retouching, no signage.
```

---

### 6.3 `toolsform.png` — formulario de información adicional

**Qué tiene que transmitir:** "contanos de tu operación" — planificación. **Reencuadrado por completo**: la v1 puso dos personas con las manos sobre un plano y falló en las dos cosas. Ahora es un **bodegón de escritorio sin personas**. Los objetos son mucho más fáciles que la gente, y la escena sigue contando lo mismo.

```
Editorial photograph for a mining industry trade magazine. The desk inside a
modular site office at a mining operation in the arid Andes of San Juan,
Argentina, photographed from above at a slight angle, no people in frame. A
scratched white hard hat rests beside a large folded paper site plan, a scuffed
two-way radio, a pair of worn leather work gloves, a metal thermos and a mug
leaving a ring on the paper. A rugged laptop sits open at the edge of the frame,
its screen switched off and dark. The desk surface is scratched and dusty. Warm
late morning daylight enters from a window on the left, falling across the desk
in a hard band, the far side of the desk in shadow. Shot on a 35mm lens at f/8,
deep focus, fine 35mm film grain, muted desaturated colours, slightly flat
contrast, unposed candid documentary photojournalism, no retouching, no readable
text or logos anywhere.
```

---

### 6.4 `toolscreaempr.png` — alta de empresa ⚠️ es la que falta hoy

**Qué tiene que transmitir:** infraestructura ordenada, todo en su lugar. Es la escena que mejor salió en la tanda 1 (la del depósito), así que el prompt conserva la composición y corrige lo que falló: los racks que se degradaban al fondo, el cielo demasiado liso y la luz de mediodía.

```
Editorial photograph for a mining industry trade magazine. A supply yard at a
mining operation in the arid Andes of San Juan, Argentina. Two rows of steel
storage racks recede toward a corrugated warehouse building, forming a strong
one-point perspective down a dirt lane; the racks hold stacked steel pipe and
plain unmarked wooden crates and are clearly built and lit at the far end, not
fading into blur. A single Argentinian storekeeper in dusty navy blue coveralls
with worn orange reflective bands, a scratched hard hat and grey work gloves
walks away from the camera down the lane, small in the frame, seen from behind.
Ochre mountain slopes rise behind the warehouse under a pale blue sky with thin
scattered high cloud. Late morning, hard directional sunlight, rack shadows
falling consistently across the lane. Shot on a 35mm lens at f/8, deep focus,
sharp from foreground to background, fine 35mm film grain, muted desaturated
colours, slightly flat contrast, unposed candid documentary photojournalism, no
retouching, no readable text or logos.
```

---

### 6.5 `toolsbienvenida.png` — bienvenida

**Qué tiene que transmitir:** el beneficio ya conseguido, la operación fluyendo. **Reencuadrado**: la v1 pedía dos personas intercambiando una caja — cuatro manos y un objeto entre ellas, la peor combinación posible. Ahora es **una sola persona, de perfil, cargando**, con la caja apoyada en el antebrazo.

```
Editorial photograph for a mining industry trade magazine. A supply pickup truck
parked outside a corrugated warehouse at a mining operation in the arid Andes of
San Juan, Argentina, its tailgate down. A single Argentinian storekeeper in his
fifties, sun-weathered face with visible skin texture, seen in profile at medium
distance, lifts a plain sealed cardboard box resting against his forearm onto the
open tailgate, looking down at what he is doing, calm and unhurried. He wears
dusty navy blue coveralls with worn orange reflective bands, a scratched hard hat
and grey work gloves. Ochre mountain slopes and a pale blue sky behind. Late
morning, warm hard directional sunlight, shadows falling consistently to the
left, fine dust in the air. Shot on a 50mm lens at f/8, deep focus, the warehouse
and mountains behind him still legible and sharp, fine 35mm film grain, muted
desaturated colours, slightly flat contrast, unposed candid documentary
photojournalism, no retouching, no readable text or logos.
```

---

## 7. Prompt negativo

**Qwen-Image lo acepta y conviene usarlo.** FLUX.1 [schnell] lo ignora (§4), así que ahí no suma ni resta. Pegalo tal cual en el campo de negativo, para los cinco:

```
text, letters, watermark, logo, brand names, signage, readable screen, user
interface, extra fingers, deformed hands, close-up hands, hands holding objects,
smiling at camera, posed, stock photo look, glossy, oversaturated, neon orange,
HDR, heavy bokeh, blurred background, plastic skin, airbrushed, 3d render,
illustration, cartoon, brand-new clean clothing, lush vegetation, cactus, snow,
detached shadow, floating shadow
```

Los dos últimos son específicos: `detached shadow` / `floating shadow` apuntan al defecto que arruinó una de las imágenes de la tanda 1, donde la sombra del operario quedó desprendida del cuerpo.

## 8. Antes de subirlas: post-producción y checklist

**Post-producción (5 minutos, cualquier editor):**

0. **Sacarle el brillo de IA** — es el paso que más rinde y lleva dos minutos. En cualquier editor (GIMP, Photopea en el navegador, incluso Snapseed): bajá la **saturación** un 10-15 %, bajá un poco el **contraste**, y agregá **grano** fino (ruido monocromático suave). Si quedó un naranja fluo, bajale la saturación **sólo a ese rango de color**. Una foto real casi nunca tiene los colores tan limpios como los que devuelve un generador.
1. **Escalar** la del registro a 1536×1536 (§5).
2. **Comprimir.** Objetivo: **≤ 400 KB** la del registro, **≤ 250 KB** las otras cuatro. Con eso pasás de 8 MB totales a menos de 1,4 MB. Squoosh (`squoosh.app`) lo hace en el navegador y es gratis.
3. **Formato:** dejalas en **PNG** — los nombres de archivo están cableados en `constants.php` con extensión `.png` y cambiar a WebP obliga a tocar código en cinco lugares. Un PNG bien comprimido entra holgado en el presupuesto de arriba.
4. **Componer el producto, si querés mostrarlo.** Las tablets y laptops de los prompts salen con pantalla apagada a propósito: si querés que se vea la interfaz, pegá encima un screenshot real ahora (§3, regla 3).

**Checklist antes de dar por buena cada imagen:**

- [ ] El EPP es correcto para esa tarea y ese lugar — **aprobado por alguien que conozca la operación real**, no por vos ni por mí
- [ ] No hay texto, ni logos, ni marcas legibles en ninguna parte
- [ ] Las manos tienen cinco dedos y se ven naturales
- [ ] **Cada sombra sale del pie de quien la proyecta**, y todas caen para el mismo lado
- [ ] La ropa se ve usada, no recién comprada; nada de naranja fluo irreal
- [ ] Nadie está parado al borde de un talud, en altura ni en posición insegura
- [ ] Ninguna cara se parece a una persona identificable
- [ ] El paisaje es de montaña andina árida, no desierto americano ni bosque
- [ ] Las cinco parecen de la misma sesión: misma luz, misma paleta, mismo vestuario
- [ ] La del registro se ve bien recortada a 1:1 **y** probada en una pantalla ancha y en una de notebook
- [ ] Peso dentro del objetivo

---

## 9. Cómo instalarlas

Las cinco van a `public/img/` con **exactamente estos nombres** (están cableados en `application/config/constants.php`, líneas 237-242):

```
public/img/toolsregister.png      ← REGISTER_IMG_BACKGROUND
public/img/toolschangepass.png    ← REGISTER_IMG_COMPLETE_PASSWORD
public/img/toolsform.png          ← REGISTER_IMG_FORMULARIO
public/img/toolscreaempr.png      ← REGISTER_IMG_CREAR_EMPRESA   (nueva: hoy falta)
public/img/toolsbienvenida.png    ← REGISTER_IMG_BIENVENIDA
```

Con reemplazar los archivos alcanza: **no hay que tocar ni una línea de código.** Si el navegador te sigue mostrando las viejas, es caché — recargá con `Ctrl+F5`.

Para verificar las cinco pantallas en orden, sin tener que registrarte de verdad cada vez, mirá los modos de fallo y las rutas en `doc/PROCESO_REGISTRACION.md` §2 (tabla de etapas y rutas).

---

## 10. Referencias

- `application/config/constants.php` líneas 237-244 — las constantes de imagen
- `application/views/register.php` · `complete_password.php` · `formulario_page.php` · `crear_empresa_page.php` · `bienvenida_page.php`
- `doc/PROCESO_REGISTRACION.md` — el flujo funcional completo y sus dependencias
- FLUX.1 [schnell] — modelo y licencia Apache 2.0: `huggingface.co/black-forest-labs/FLUX.1-schnell`
- Squoosh (compresión en el navegador): `squoosh.app`
