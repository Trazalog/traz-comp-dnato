# Imágenes del flujo de entrada — generación con IA y tratamiento

## Objetivo

Es el instructivo para **producir las 6 imágenes definitivas de las pantallas de entrada** (login, recuperar contraseña, selección de empresa, registro, activación, formulario, alta de empresa y bienvenida). Dice qué IA gratuita usar y cómo entrar a ella, qué estilo visual se decidió y por qué, cómo aplicarlo, y trae los **prompts listos para copiar y pegar**, uno por imagen. Se ejecuta desde el navegador: sólo hace falta tocar código para instalar el resultado (§9).

**No cubre**: el flujo funcional de la registración (`doc/PROCESO_REGISTRACION.md`), el diseño de las pantallas, ni el logo de marca — `logotzl.png` y `toolsgrey.png` son activos existentes y **no se tocan**.

---

## 0. Estado

| Imagen | Sector | Estado |
|---|---|---|
| `toolslogin.png` | ⛏️ Minería | ⏳ **Nueva.** Login, recuperar contraseña y fondo de selección de empresa — §6.1 |
| `toolsregister.png` | 🛢️ **Petróleo y gas** | ⏳ Rehacer — §6.2 |
| `toolschangepass.png` | ⛏️ Minería | ⏳ Falta — §6.3 |
| `toolsform.png` | — neutro | ⏳ Falta — §6.4 |
| `toolscreaempr.png` | ⚡ **Energía** | ⏳ Falta — §6.5. **Es la que hoy da 404** |
| `toolsbienvenida.png` | ⛏️ Minería | ⏳ Falta — §6.6 |

**Historia corta:** la primera tanda (5 con FLUX.1 [schnell] + 1 con Qwen-Image) se descartó casi entera porque *se notaba que era IA*. El diagnóstico está en §1 y motivó el cambio de enfoque de este documento: **ya no se busca fotorrealismo, se busca una foto tratada**. El material viejo sigue en `/mnt/win/dev/Trazalog/dnato/` y la mejor de esa tanda —el pit andino de Qwen— sirve como base para §6.2 si se le aplica el tratamiento.

> ⚠️ Las proporciones de §5 corresponden al rediseño de las pantallas de entrada, que llega por el **PR #26** (todavía sin mergear). Si se generan las imágenes antes de que ese PR entre, las medidas siguen sirviendo: son las del layout nuevo.

---

## 1. Por qué las fotos de IA se ven feas

No es que estén mal generadas: es que **casi aciertan**. Una foto sintética que apunta al fotorrealismo y falla en la piel, en las manos o en la física de la luz cae en el valle inquietante — el ojo registra que algo está mal antes de poder decir qué. Y una vez que lo detecta, ya no lo puede ignorar.

Los defectos concretos de la primera tanda: piel sin poros y con brillo plástico, naranjas fluorescentes que no existen en tela real, cielos con degradado perfecto y sin variación, desenfoque de fondo con forma de retrato de celular, y una sombra desprendida del cuerpo que la proyectaba.

**Corregir eso pidiendo "más realismo" es pelear contra la naturaleza de la herramienta.** El camino que funciona es el contrario: dejar de pretender que es una foto documental y **asumirla como una imagen tratada**. Si el alejamiento del realismo es una decisión estética evidente, deja de leerse como error y pasa a leerse como marca.

---

## 2. El estilo propuesto

### Duotono cálido: petróleo y arena, con el naranja del EPP como único acento

La foto se conserva entera —su grano, su profundidad, su encuadre— pero el rango de color se comprime a dos polos: **las sombras viran a azul petróleo** y **las luces a un arena cálido**. El naranja de la ropa de trabajo se mantiene como único color vivo.

**Por qué éste y no otro:**

- **No es caricatura.** Sigue siendo una fotografía. No hay trazo, ni vectores, ni estilización de formas: sólo corrección de color, que es el recurso más sobrio del retoque editorial.
- **No es "demasiado creativo".** Un duotono suave es lo que usa cualquier informe anual o web corporativa seria. Es el registro de un software para gente que trabaja, no el de una app de consumo.
- **Respeta los colores que ya tenés.** El petróleo de las sombras es el `#24303d` que ya usa el panel visual del login, y conversa con el `#3498db` de los botones. El arena de las luces es el ocre del paisaje andino de las fotos actuales. El naranja del EPP es el mismo. **No se agrega ni un color nuevo al sistema.**
- **Y lo más importante: tapa los defectos de la IA.** Piel plástica, saturación imposible, colores que no cierran — todos desaparecen cuando el rango cromático se comprime. Lo que quedaba en evidencia deja de estar.
- **Unifica.** Seis imágenes generadas en momentos distintos, con modelos distintos y calidad dispar, terminan pareciendo una sola sesión de fotos. Ese era el otro problema abierto.

**Lo que este estilo NO es:** no es blanco y negro, no es un filtro de Instagram, y no es duotono puro de dos tintas planas — eso sí se vería gráfico y publicitario. Es una foto con una intención cromática clara.

**El punto clave:** este estilo se aplica **en post-proceso** (§4), no depende del prompt. Eso significa que **aunque el modelo falle en realismo, el resultado final igual es coherente**. Deja de importar la lotería de la generación, que es lo que hundió la primera tanda.

### Alternativas, si ésta no convence

| Alternativa | Cómo se ve | Cuándo elegirla |
|---|---|---|
| **B — Monocromo con acento naranja** | Blanco y negro salvo la ropa de trabajo, que queda naranja | Más dramático e industrial. Muy usado en minería pesada. El riesgo es que se sienta "duro" para un producto de gestión |
| **C — Desaturado documental con grano** | Foto en color pero con saturación baja, contraste plano y grano | El más conservador: sigue siendo una foto normal. Es lo que intentó la tanda anterior y por sí solo **no alcanzó** para tapar los defectos |

Las recetas de las tres están en §4.3, así que cambiar de idea no obliga a regenerar nada: se retratan las mismas imágenes.

---

## 3. Cómo acceder a las IA gratuitas

### 3.1 La recomendada: Qwen-Image (Alibaba)

Licencia **Apache 2.0**: uso comercial explícito, sin atribución, sin regalías y sin watermark. No es un "plan gratuito" con letra chica, es una licencia de software libre. Fue la que mejor resultado dio en la primera tanda.

**Paso a paso:**

1. Entrá a **`huggingface.co/Qwen`** — es la organización oficial de Alibaba Cloud.
2. En la solapa **Spaces**, abrí **`Qwen-Image`** (generar desde texto).
3. Creá una cuenta gratuita de Hugging Face si no tenés (basta un correo). Sin cuenta la cola es más lenta.
4. Abrí **Advanced Settings** y dejá:
   - **Aspect ratio: `1:1`** — es lo que necesitan las seis (§5).
   - **Prompt Enhance: DESTILDADO.** ⚠️ Si queda tildado, el Space **reescribe tu prompt** antes de generar, y se pierde todo el trabajo de encuadre, vestuario y luz. Es el error más fácil de cometer.
   - *Randomize seed*: tildado, para que cada corrida dé algo distinto.
   - `guidance_scale` y `num_inference_steps`: dejalos como vienen.
5. Pegá el prompt y generá.
6. Repetí 4 veces: cada corrida da un resultado distinto y la diferencia entre la primera y la mejor de cuatro es grande.

**El Space hermano que ahora importa:** en la misma organización está **`Qwen-Image-Edit`**, que **edita una imagen existente con instrucciones en lenguaje natural**. Es uno de los tres caminos para aplicar el tratamiento de color (§4.2) sin abrir un editor.

### 3.2 La alternativa: FLUX.2 [klein] (Black Forest Labs)

También **Apache 2.0**, mismas garantías legales. Está en `huggingface.co/black-forest-labs`, mismo procedimiento.

> **No usar FLUX.1 [schnell]**: es la variante destilada de 4 pasos, la más rápida y la más floja de la familia. Es la que produjo las cinco imágenes descartadas.

### 3.3 ⚠️ Cuidado con los sitios de terceros

Buscar "generar imágenes gratis con Qwen" devuelve decenas de sitios que hostean estos modelos. **La licencia Apache 2.0 cubre el modelo, no al intermediario**: a esa licencia se le suman los términos del sitio, que pueden reclamar derechos sobre lo que generás o reservar el uso comercial para su plan pago.

**Regla: usá los Spaces oficiales de las organizaciones de arriba, o corré el modelo localmente.** Si un sitio no te muestra sus términos, no lo uses para material que va a producción.

### 3.4 Por qué no las otras

| Herramienta | Por qué no |
|---|---|
| Google Gemini (gratis) | Estampa un **watermark visible** (✦) en las imágenes del plan gratuito |
| Microsoft Copilot / Bing | Su acuerdo de servicios limita el uso a personal, salvo planes comerciales |
| Adobe Firefly (gratis) | El plan gratuito es para evaluación; la indemnización de PI viene sólo con los pagos |
| Ideogram (gratis) | Términos contradictorios y las generaciones son públicas por defecto |

**La excepción que vale considerar:** *Adobe Firefly Standard*, USD 9,99/mes. Entrenado sólo con stock licenciado y con **indemnización de Adobe** ante reclamos de propiedad intelectual. Le vendés a mineras, que tienen área legal; puede ser la compra más barata del proyecto. Es tu decisión — este documento asume Qwen.

---

## 4. Cómo se aplica el tratamiento

Tres caminos para lo mismo. **El 4.0 es el más simple y el recomendado**: no hay que aprender a usar nada.

### 4.0 El script del repo — un comando por imagen ⭐

`scripts/tratar-imagen.php` hace las tres capas de ajuste de una, con los valores exactos del documento. Su ventaja sobre hacerlo a mano no es la comodidad: es que **las seis imágenes salen con exactamente los mismos números**, que es lo que las hace parecer una sola sesión de fotos.

Desde una terminal, parado en la raíz del repo:

```bash
# una imagen
php scripts/tratar-imagen.php ~/Descargas/foto.png

# → escribe ~/Descargas/foto-tratada.png

# indicando el destino final
php scripts/tratar-imagen.php ~/Descargas/foto.webp public/img/toolslogin.png

# una carpeta entera
for f in ~/Descargas/tanda/*.png; do php scripts/tratar-imagen.php "$f"; done
```

Acepta PNG, JPG y WebP, y **nunca toca el archivo de entrada**. Tarda unos 2 segundos por imagen de 1024×1024.

**Si querés probar otras intensidades** antes de decidir:

```bash
FUERZA=55 php scripts/tratar-imagen.php foto.png prueba-55.png   # más suave
FUERZA=85 php scripts/tratar-imagen.php foto.png prueba-85.png   # más marcado
```

`FUERZA` es la opacidad del duotono, de 0 a 100. Por debajo de 60 casi no se nota; por encima de 85 empieza a verse como afiche. **70 es el valor del documento** y el que hay que usar en las seis una vez elegido. También acepta `GRANO` (0-30, por defecto 6) y `CONTRASTE` (0-40, por defecto 12).

> ⚠️ **El grano encarece mucho el PNG.** El ruido rompe la compresión: una imagen tratada puede pasar de 190 KB a 1,6 MB. Es esperable — por eso el paso de comprimir en `squoosh.app` (§8) no es opcional, es obligatorio. El script avisa cuando el resultado pasa los 400 KB.

### 4.1 Photopea — a mano, en el navegador

`photopea.com`. Es un editor tipo Photoshop que corre en el navegador. Sirve si querés ver el efecto capa por capa o ajustar una imagen en particular; para las seis, el 4.0 es más rápido y más consistente.

1. **Archivo → Abrir** y subí la imagen generada.
2. **Capa → Nueva capa de ajuste → Mapa de degradado.**
3. Hacé clic en la barra de degradado para editarla y cargá tres paradas:

   | Posición | Color | Rol |
   |---|---|---|
   | 0 % | `#1b2a38` | sombras — azul petróleo |
   | 50 % | `#6b7a80` | medios — gris azulado neutro |
   | 100 % | `#e9dfcd` | luces — arena cálido |

4. **Bajá la opacidad de esa capa al 70 %.** Éste es *el* paso que hace la diferencia: al 100 % queda un duotono puro y se ve gráfico; al 70 % sobrevive algo del color original —sobre todo el naranja del EPP— y sigue leyéndose como fotografía. Movete entre 65 % y 80 % hasta que te guste.
5. **Capa → Nueva capa de ajuste → Curvas**, y bajá un poco el contraste: subí el punto negro y bajá el blanco. Una foto real casi nunca tiene negros puros.
6. Opcional pero recomendado: **Filtro → Ruido → Añadir ruido**, monocromático, 2-3 %. El grano es lo que más ayuda a que se lea como foto y no como render.
7. **Archivo → Exportar como → PNG.**

Guardá el `.psd` de la primera: para las cinco restantes copiás y pegás las capas de ajuste y quedan todas idénticas. Ese es el secreto de que las seis parezcan una sola sesión.

### 4.2 Qwen-Image-Edit — sin salir de la IA

En el Space `Qwen-Image-Edit` (§3.1), subí la imagen y pedile el tratamiento con esta instrucción:

```
Apply a subtle duotone colour grade to this photograph: push the shadows toward
deep petrol blue and the highlights toward warm sand beige, keeping the orange
of the workwear as the only vivid colour. Keep it photographic and restrained,
not a poster effect. Lower the overall saturation, flatten the contrast slightly,
and add fine film grain. Do not change the composition or any object.
```

Más rápido, menos control. Sirve para probar el estilo antes de comprometerse; para el resultado final es preferible §4.1, donde el valor es exactamente el mismo en las seis.

### 4.3 Recetas de las alternativas

- **B — Monocromo con acento:** capa de ajuste **Blanco y negro**, y encima una máscara que revele el color original sólo sobre la ropa de trabajo.
- **C — Desaturado documental:** sin mapa de degradado; **Tono/Saturación** a −25, **Curvas** con contraste plano, y grano al 3 %.

---

## 5. Qué necesita cada imagen

Medido contra el CSS real de cada vista. **Cambió respecto de la versión anterior de este documento**: con el rediseño, login, recuperar contraseña y registro usan la imagen a sangre en el panel derecho.

| Constante | Archivo | Dónde se usa | Cómo se renderiza | Generar |
|---|---|---|---|---|
| `LOGIN_IMG_BACKGROUND` | `toolslogin.png` | Login, recuperar contraseña y fondo oscurecido de la selección de empresa | Panel derecho: **54 % del ancho × 100 % del alto**, `cover`, centrada | **1:1, 1536×1536** |
| `REGISTER_IMG_BACKGROUND` | `toolsregister.png` | Registro | Ídem, 54 % × 100 % | **1:1, 1536×1536** |
| `REGISTER_IMG_COMPLETE_PASSWORD` | `toolschangepass.png` | Activación de cuenta | Panel lateral, ~320 px de ancho | **1:1, 1024×1024** |
| `REGISTER_IMG_FORMULARIO` | `toolsform.png` | Formulario adicional | Ídem | **1:1, 1024×1024** |
| `REGISTER_IMG_CREAR_EMPRESA` | `toolscreaempr.png` | Alta de empresa — **hoy da 404** | Ídem | **1:1, 1024×1024** |
| `REGISTER_IMG_BIENVENIDA` | `toolsbienvenida.png` | Bienvenida | Ídem | **1:1, 1024×1024** |

> **Por qué 1:1 y no panorámica.** El panel derecho ocupa el 54 % del ancho a pantalla completa: en 1920×1080 son 1037×1080 px, o sea **prácticamente cuadrado**. Como es `cover`, el recorte cambia con cada resolución, así que conviene el motivo centrado y con aire alrededor.
>
> Qwen rinde mejor cerca de 1 megapíxel: para las de 1536, generá a 1024×1024 y **escalá después** con cualquier upscaler (hay Spaces de upscaling en Hugging Face, o Upscayl si preferís instalar algo).

**Login y registro llevan imágenes distintas** aunque hoy compartan archivo: son dos pantallas del mismo flujo y repetir la misma foto se nota.

---

## 6. Los prompts (v3)

En inglés y en prosa continua, que es como mejor responden estos modelos. **Copiar y pegar completo.**

Tres cosas cambiaron respecto de la versión anterior, y las tres importan:

1. **Piden una foto plana y fácil de tratar**, no una foto "linda": luz pareja, colores contenidos, buen rango tonal. El carácter se lo da el tratamiento de §4, no el prompt. Una imagen ya dramática y saturada se arruina al aplicarle el duotono.
2. **La biblia visual va escrita adentro de cada prompt** — región, hora, vestuario, origen de las personas. Cuando estaba en una tabla aparte, el modelo devolvió gente del norte de Europa.
3. **Los encuadres evitan lo que la IA hace mal.** Regla verificada sobre la primera tanda: funcionan las personas de espaldas o de perfil lejano, una sola por imagen, manos vacías y foco profundo; fallan las caras frontales, dos personas interactuando, **las manos sosteniendo objetos**, el texto y el bokeh fuerte.
4. **No todas son de minería.** El producto no se vende sólo a mineras, y seis imágenes del mismo pit lo hacen parecer un software de un solo rubro. La secuencia queda: minería → **petróleo y gas** → minería → neutro → **energía** → minería. La primera y la última se mantienen en minería porque son las que enmarcan el flujo (§6.1 y §6.6), y la del escritorio (§6.4) no muestra sector.

**Dos regiones, un solo tratamiento.** Las de minería transcurren en la cordillera de San Juan (roca ocre) y las de petróleo y energía en la estepa de Neuquén (grava pálida, arbustos bajos, horizonte llano). Son paisajes distintos a propósito, y el duotono de §4 los unifica igual: ése es justamente el beneficio de tratar todas las imágenes con las mismas capas.

Generá **4 variantes de cada uno** y elegí antes de tratar.

---

### 6.1 `toolslogin.png` — login, recuperar contraseña y selección de empresa

Es la primera pantalla del producto y la que más se ve. Tiene que decir "esto es para tu operación" sin gritar. Como también se usa oscurecida detrás de la selección de empresa, conviene que sea legible incluso con un velo encima.

```
Editorial photograph for a mining industry trade magazine. Early morning at an
open-pit mining operation high in the arid Andes of San Juan, Argentina. Wide
ochre and grey rock benches step down toward the middle of the frame; a hazy
cordillera ridgeline closes the horizon under a pale, even sky. A single
Argentinian mine worker in his forties stands small in the middle distance on a
bench edge, seen from behind, looking out over the pit, hands at his sides. His
navy blue coveralls are dusty and faded, the orange reflective bands worn, his
hard hat scratched, his boots caked in pale dust. Soft even morning light, no
harsh highlights, shadows falling consistently to the left. Shot on a 35mm lens
at f/8, deep focus, sharp from foreground to horizon, fine film grain, restrained
natural colour, flat contrast, plenty of empty space around the subject, unposed
candid documentary photojournalism, no retouching.
```

---

### 6.2 `toolsregister.png` — registro · 🛢️ **petróleo y gas, Vaca Muerta**

Escala y ambición: quien mira todavía no es cliente. Es la pantalla que muestra que Trazalog no es sólo para minería, así que cambia de cuenca y de paisaje — estepa patagónica en vez de cordillera.

```
Editorial photograph for an energy industry trade magazine. A shale oil and gas
drilling pad in the Vaca Muerta formation, Neuquén, Argentine Patagonia, seen
from a low rise in the early morning. A steel drilling rig derrick stands against
a wide flat horizon of arid Patagonian steppe — pale gravel ground, sparse low
scrub, no trees; storage tanks, pipe racks and a gravel access road spread around
the pad; a service truck parked to one side gives scale. Two Argentinian field
workers in navy blue coveralls with worn orange reflective bands and hard hats
stand small in the middle distance beside the tanks, seen from behind. Soft even
morning light with long gentle shadows, pale uniform sky, thin wind-blown dust.
Shot on a 35mm lens at f/8, deep focus, everything sharp, fine film grain,
restrained natural colour, flat contrast, orderly and calm, unposed documentary
photojournalism, no retouching, no readable text or logos.
```

---

### 6.3 `toolschangepass.png` — activación de cuenta

Acceso autorizado, identidad verificada. Persona de espaldas y sin dispositivo: en la tanda anterior, los dedos sobre el teléfono fueron el defecto más visible.

```
Editorial photograph for a mining industry trade magazine. A vehicle access
control point at a mining site in the arid Andes of San Juan, Argentina. A
weathered steel boom gate crosses the frame; a small modular guard cabin with
dusty windows stands to the right; pale ochre slopes rise behind. An Argentinian
site supervisor in her forties, seen from behind at medium distance, stands at
the gate facing the cabin, hands at her sides, wearing dusty navy blue coveralls
with worn orange reflective bands, a scratched white hard hat and a ponytail.
Soft even morning light, shadows falling consistently to the left, fine dust in
the air. Shot on a 35mm lens at f/8, deep focus, both the supervisor and the
cabin sharp, fine film grain, restrained natural colour, flat contrast, unposed
candid documentary photojournalism, no retouching, no signage.
```

---

### 6.4 `toolsform.png` — formulario de información adicional

"Contanos de tu operación": planificación. **Sin personas**, a propósito — los objetos salen mucho mejor que la gente y la escena cuenta lo mismo.

```
Editorial photograph for a mining industry trade magazine. The desk inside a
modular site office at a mining operation in the arid Andes of San Juan,
Argentina, photographed from above at a slight angle, no people in frame. A
scratched white hard hat rests beside a large folded paper site plan, a scuffed
two-way radio, a pair of worn leather work gloves, a metal thermos and a mug
leaving a ring on the paper. The desk surface is scratched and dusty. Soft
daylight enters from a window on the left and falls evenly across the desk. Shot
on a 35mm lens at f/8, deep focus, fine film grain, restrained natural colour,
flat contrast, unposed candid documentary photojournalism, no retouching, no
readable text or logos anywhere.
```

---

### 6.5 `toolscreaempr.png` — alta de empresa · ⚡ **energía, tendido eléctrico** ⚠️ es la que hoy falta

Poner la operación en marcha: infraestructura levantándose y una cuadrilla trabajando. La perspectiva en fuga de las torres cumple la misma función que la del depósito anterior —orden, cosas en su lugar— pero en otro sector.

**Sobre la seguridad, que acá pesa más que en las otras:** el trabajo en altura sin arnés es el error que un cliente detecta al instante. El prompt pide el arnés explícitamente y ubica a la cuadrilla a media distancia, donde además las manos y las caras dejan de ser un problema para el modelo.

```
Editorial photograph for an energy industry trade magazine. A high-voltage
transmission line under maintenance on open arid plains in Neuquén, Argentine
Patagonia. A row of steel lattice transmission towers recedes toward the horizon
in strong perspective, conductors sweeping between them against a pale even sky;
pale gravel ground with sparse low scrub, no trees. At the base of the nearest
tower a utility truck with a raised insulated boom lift is parked; one Argentinian
line worker stands in the elevated basket wearing a full safety harness clipped
to the basket, a hard hat and navy blue coveralls with worn orange reflective
bands, working at the crossarm; two more workers in the same gear stand on the
ground beside the truck, seen from behind at medium distance, looking up. Soft
even morning light, tower shadows falling consistently across the ground. Shot on
a 35mm lens at f/8, deep focus, sharp from the truck to the far towers, fine film
grain, restrained natural colour, flat contrast, unposed candid documentary
photojournalism, no retouching, no readable text or logos.
```

---

### 6.6 `toolsbienvenida.png` — bienvenida

El beneficio ya conseguido: la operación funcionando sin fricción. Una sola persona de perfil, con la caja apoyada en el antebrazo — la versión anterior pedía dos personas intercambiando un objeto, que es la peor combinación posible para estos modelos.

```
Editorial photograph for a mining industry trade magazine. A supply pickup truck
parked outside a corrugated warehouse at a mining operation in the arid Andes of
San Juan, Argentina, its tailgate down. A single Argentinian storekeeper in his
fifties, sun-weathered face with visible skin texture, seen in profile at medium
distance, lifts a plain sealed cardboard box resting against his forearm onto the
open tailgate, looking down at what he is doing, calm and unhurried. He wears
dusty navy blue coveralls with worn orange reflective bands, a scratched hard hat
and grey work gloves. Ochre slopes and a pale even sky behind. Soft late morning
light, shadows falling consistently to the left. Shot on a 50mm lens at f/8, deep
focus, the warehouse and slopes behind him still legible and sharp, fine film
grain, restrained natural colour, flat contrast, unposed candid documentary
photojournalism, no retouching, no readable text or logos.
```

---

## 7. El prompt negativo — leer antes de buscarlo

**El Space oficial de Qwen-Image NO tiene campo de prompt negativo.** Se lo sacaron de la interfaz: en el código está fijo como `text, watermark, copyright, blurry, low resolution` y no se puede editar desde la pantalla. Si lo estuviste buscando, no está.

No es un problema, por dos razones:

**1. Los prompts de §6 ya vienen escritos sin necesitar negativo.** Cada exclusión está expresada en afirmativo dentro del propio prompt: dice "pantalla apagada" en lugar de "sin texto en pantalla", "manos a los costados" en lugar de "sin manos sosteniendo objetos", "luz pareja" en lugar de "sin luz dramática". Se escribieron así a propósito, justamente para no depender de un campo que no siempre existe.

**2. Si aun así querés reforzar**, pegá esta cola al final de cualquier prompt de §6, en la misma caja de texto:

```
The scene contains no lettering, no signage and no logos anywhere; every screen
is switched off; every person is seen from behind or in profile with their hands
empty and at their sides; the workwear is worn and dusty rather than new; the
light is soft and even, never dramatic; the whole frame is in focus.
```

**Sólo si el sitio que usás sí tiene campo de negativo** (algunos frontends de terceros lo exponen, con la advertencia de licencia de §3.3), pegá esto ahí:

```
text, letters, watermark, logo, brand names, signage, readable screen, user
interface, extra fingers, deformed hands, close-up hands, hands holding objects,
smiling at camera, posed, stock photo look, glossy, oversaturated, neon orange,
HDR, heavy bokeh, blurred background, plastic skin, airbrushed, 3d render,
illustration, cartoon, brand-new clean clothing, lush vegetation, cactus, snow,
detached shadow, floating shadow, dramatic lighting, golden hour, lens flare
```

Las tres últimas importan más que antes: el tratamiento de §4 necesita una imagen de partida **plana**, y una foto ya dramática y saturada se arruina al aplicarle el duotono.

---

## 8. Antes de subirlas

**Post-producción**, después del tratamiento de §4:

1. **Escalar** las dos de 1536 (§5).
2. **Comprimir.** Objetivo: **≤ 400 KB** las dos grandes, **≤ 250 KB** las cuatro chicas. `squoosh.app` lo hace en el navegador, gratis. Las actuales pesan 1,6 MB cada una para verse a 320 px.
3. **Formato PNG**: los nombres están cableados en `constants.php` con esa extensión.

**Checklist de aprobación:**

- [ ] El EPP es correcto para esa tarea y ese lugar — **aprobado por alguien que conozca la operación real**. La IA no sabe de normativa argentina, y a un cliente minero un casco mal puesto le salta a la vista
- [ ] Nadie está al borde de un talud, en altura ni en posición insegura
- [ ] No hay texto, logos ni marcas legibles en ninguna parte
- [ ] Las manos tienen cinco dedos y se ven naturales
- [ ] **Cada sombra sale del pie de quien la proyecta**, y todas caen para el mismo lado
- [ ] La ropa se ve usada, no recién comprada
- [ ] Ninguna cara se parece a una persona identificable
- [ ] El paisaje corresponde a la región de esa imagen: montaña andina árida (§6.1, 6.3, 6.6) o estepa patagónica llana (§6.2, 6.5). En ningún caso desierto americano, cactus ni bosque
- [ ] En la de energía (§6.5): quien trabaja en altura **lleva arnés y está amarrado**
- [ ] **Las seis pasaron por el mismo tratamiento, con el mismo valor de opacidad**
- [ ] Las dos grandes se ven bien recortadas a 1:1, probadas en pantalla ancha y en notebook
- [ ] Peso dentro del objetivo

---

## 9. Cómo instalarlas

Las seis van a `public/img/` con **exactamente estos nombres**:

```
public/img/toolslogin.png       ← LOGIN_IMG_BACKGROUND            (nueva)
public/img/toolsregister.png    ← REGISTER_IMG_BACKGROUND
public/img/toolschangepass.png  ← REGISTER_IMG_COMPLETE_PASSWORD
public/img/toolsform.png        ← REGISTER_IMG_FORMULARIO
public/img/toolscreaempr.png    ← REGISTER_IMG_CREAR_EMPRESA      (hoy falta → 404)
public/img/toolsbienvenida.png  ← REGISTER_IMG_BIENVENIDA
```

Cinco de las seis entran con sólo copiar el archivo. La única línea de código a tocar es la de la imagen del login, en `application/config/constants.php`, que hoy apunta a la del registro:

```php
define('LOGIN_IMG_BACKGROUND', 'public/img/toolslogin.png');
```

Si el navegador sigue mostrando las viejas, es caché: `Ctrl+F5`.

---

## 10. Referencias

- `application/config/constants.php` — las constantes de imagen
- `application/views/login.php` · `forgot.php` · `login_empresa.php` · `register.php` · `complete_password.php` · `formulario_page.php` · `crear_empresa_page.php` · `bienvenida_page.php`
- `doc/PROCESO_REGISTRACION.md` — el flujo funcional completo
- Qwen (organización oficial): `huggingface.co/Qwen` — Spaces `Qwen-Image` y `Qwen-Image-Edit`
- Black Forest Labs: `huggingface.co/black-forest-labs`
- Photopea (editor en el navegador): `photopea.com`
- Squoosh (compresión, **obligatoria** después del tratamiento): `squoosh.app`
- `scripts/tratar-imagen.php` — el tratamiento automatizado (§4.0)
- Material de la primera tanda (todo minería, previo al cambio de sectores): `/mnt/win/dev/Trazalog/dnato/`
