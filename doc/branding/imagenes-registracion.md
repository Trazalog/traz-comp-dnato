# Imágenes del flujo de registración — generación con IA para producción

## Objetivo

Es el instructivo para **reemplazar las imágenes provisorias del flujo de registración por imágenes definitivas de producción**, generadas con IA. Dice qué herramienta usar y por qué, qué proporción y peso necesita cada imagen (medido contra el CSS real de cada vista), y trae los **prompts listos para copiar y pegar**, uno por pantalla. Se ejecuta desde el navegador: no hace falta tocar código para generar, sólo para instalar el resultado (§9).

**No cubre**: el flujo funcional de la registración (eso está en `doc/PROCESO_REGISTRACION.md`), ni el diseño de las pantallas en sí, ni el logo de marca — `logotzl.png` y `toolsgrey.png` son activos de marca existentes y **no se tocan**.

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

### 2.2 Recomendación: FLUX.1 [schnell]

**Es la única opción realmente gratuita cuya licencia no deja lugar a interpretación.** Apache 2.0 significa que podés usar el modelo y las imágenes que genera con fines comerciales —material de marketing, la web de producto, lo que sea—, sin atribución, sin regalías y sin watermark. No es un "plan gratuito" con letra chica: es una licencia de software libre.

**Dónde usarlo sin instalar nada:** en Hugging Face, con una cuenta gratuita, desde el Space oficial de Black Forest Labs (`huggingface.co/black-forest-labs/FLUX.1-schnell`, botón de demo). Cola compartida, así que en horario pico puede tardar.

> ⚠️ **Ojo con esto:** la licencia Apache 2.0 cubre **el modelo**. Si lo corrés en un sitio de terceros ("flux gratis online", Spaces de usuarios random), a la licencia del modelo **se le suman los términos de ese sitio**, que pueden reclamar derechos sobre lo que generás o reservar el uso comercial. Usá el Space oficial, o corré el modelo localmente. Si el sitio no te muestra sus términos, no lo uses para material de producción.

**Alternativa — Qwen-Image** (también Apache 2.0): mejor si en algún momento necesitás **texto legible dentro de la imagen**. Para estas cinco no hace falta (el texto va en HTML, encima), pero anotalo para material de marketing futuro.

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

El naranja y el azul marino no son decorativos: **el azul marino conversa con el `#3498db` de los botones de la interfaz**, así que las fotos y la UI van a verse de la misma familia.

> **Nota técnica sobre FLUX.1 [schnell]:** corre destilado, con *guidance* fija — **los prompts negativos no le hacen efecto**. Todo lo que no querés hay que expresarlo en positivo dentro del prompt (por eso los de §6 dicen "pantalla apagada" en vez de "sin texto en pantalla"). Si más adelante usás otro modelo que sí acepta negativos, en §7 te dejo el negativo equivalente.

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

## 6. Los prompts

Uno por pantalla. Están en inglés porque los modelos responden mejor, y en prosa continua porque FLUX prefiere prosa a listas de etiquetas. **Copiar y pegar completo, sin recortar el cierre de estilo.**

Generá **4 variantes de cada una** (misma prompt, distinta semilla) y elegí. Es gratis y la diferencia entre la primera y la mejor de cuatro es grande.

---

### 6.1 `toolsregister.png` — pantalla de registro

**Qué tiene que transmitir:** escala y seriedad. Es la primera impresión del visitante, junto al texto "Regístrese Gratis". La operación se ve grande y ordenada; las personas son parte del paisaje, no protagonistas.

```
Wide documentary photograph of an open-pit mining operation high in the arid
Andes mountains of Cuyo, Argentina, mid-morning, warm raking sunlight and long
soft shadows. Ochre and grey rock terraces descend in wide benches toward the
centre of the frame; a deep clear blue sky fills the upper third. Two workers in
navy blue and high-visibility orange coveralls with reflective bands, hard hats
and safety glasses stand small in the middle distance on a bench edge, seen from
behind and slightly above, looking out over the pit; one holds a rugged tablet
at his side, screen dark. Haul trucks are visible far below, tiny, giving scale.
Clean high-altitude air, distant cordillera ridgeline on the horizon. Shot on
35mm lens at f/4, natural light, corporate documentary photography, calm and
composed, muted earth palette, subject centred with generous empty space around it.
```

**Variantes para probar:** cambiá `open-pit mining operation` por `oil and gas wellsite with a pumpjack` para una versión de O&G, o por `mineral processing plant with conveyor structures` para una tercera.

---

### 6.2 `toolschangepass.png` — activación de cuenta y contraseña

**Qué tiene que transmitir:** seguridad y control de acceso. La pantalla dice "establecé tu contraseña"; la imagen debe hablar de identidad verificada y acceso autorizado, sin caer en el candado de stock.

```
Documentary photograph of a mining site access control point in the arid Andes
of Cuyo, Argentina, mid-morning warm sunlight. A woman supervisor in her forties,
wearing a navy blue and high-visibility orange work jacket with reflective bands,
a white hard hat and clear safety glasses, stands at a site entrance gate in
three-quarter profile, concentrated, checking a rugged handheld device held at
chest height with its screen switched off and angled away from the camera. A
clean unbranded steel gate and a modular site office are softly out of focus
behind her, ochre mountain slopes beyond. Shot on 50mm lens at f/4, shallow
depth of field, natural light, corporate documentary photography, muted earth
palette with navy and orange accents, calm and professional, no text visible.
```

---

### 6.3 `toolsform.png` — formulario de información adicional

**Qué tiene que transmitir:** "contanos de tu operación". Colaboración y planificación: dos personas trabajando juntas sobre información.

```
Documentary photograph inside a modular site office at a mining operation in the
arid Andes of Cuyo, Argentina, mid-morning light entering through a window from
the left. Two engineers, a man and a woman in their thirties in navy blue and
high-visibility orange work shirts with reflective bands, hard hats resting on
the desk beside them, lean over a large printed site plan spread across a plain
work table, discussing it, both looking down at the plan. A rugged laptop sits
open to one side, its screen dark and out of focus. Plain unbranded walls, a
shelf with folders, ochre mountains visible through the window. Shot on 35mm
lens at f/4, natural light, corporate documentary photography, muted earth
palette with navy and orange accents, collaborative and focused, no text visible.
```

---

### 6.4 `toolscreaempr.png` — alta de empresa ⚠️ es la que falta hoy

**Qué tiene que transmitir:** poner la operación en marcha. Infraestructura organizada, todo en su lugar — es la metáfora visual de dar de alta la empresa con su establecimiento y su depósito.

```
Documentary photograph of a well organised mining logistics yard in the arid
Andes of Cuyo, Argentina, mid-morning, warm raking sunlight and long soft
shadows. Neat rows of steel storage racks and stacked unbranded material
containers fill the middle ground, a modular warehouse building behind them,
a service truck parked to one side. A worker in navy blue and high-visibility
orange coveralls with reflective bands, hard hat, safety glasses and gloves
walks between the racks carrying a clipboard, seen from behind at a distance,
small in the frame. Ochre mountain slopes and a deep clear blue sky beyond.
Shot on 35mm lens at f/4, natural light, corporate documentary photography,
muted earth palette with navy and orange accents, orderly and calm, no text
visible.
```

---

### 6.5 `toolsbienvenida.png` — bienvenida

**Qué tiene que transmitir:** el beneficio, ya conseguido. Es la última pantalla del flujo: la operación funcionando sin fricción, gente que trabaja tranquila porque tiene la información. Es la única de las cinco que puede permitirse calidez.

```
Documentary photograph of a materials handover at a mining operation in the arid
Andes of Cuyo, Argentina, late morning, warm golden sunlight. A storekeeper in
navy blue and high-visibility orange coveralls with reflective bands and a hard
hat hands a sealed unbranded parts box to a maintenance technician beside a
service pickup truck; both are relaxed and mid-conversation, seen in three-
quarter profile, quietly satisfied rather than posed. A rugged tablet rests on
the open tailgate, screen dark. A modular warehouse and ochre mountain slopes
are softly out of focus behind them. Shot on 50mm lens at f/4, shallow depth of
field, natural light, corporate documentary photography, warm earth palette with
navy and orange accents, human and grounded, no text visible.
```

---

## 7. Si usás otro modelo que sí acepta prompt negativo

FLUX.1 [schnell] los ignora (§4). Si probás con Qwen-Image, SDXL o cualquier otro con *guidance* real, agregá este negativo a los cinco:

```
text, letters, watermark, logo, brand names, signage, extra fingers, deformed
hands, close-up hands, bright screen, visible user interface, cartoon, 3d render,
illustration, oversaturated, HDR, plastic skin, posed smiling at camera, stock
photo look, lush vegetation, desert cactus, snow
```

---

## 8. Antes de subirlas: post-producción y checklist

**Post-producción (5 minutos, cualquier editor):**

1. **Escalar** la del registro a 1536×1536 (§5).
2. **Comprimir.** Objetivo: **≤ 400 KB** la del registro, **≤ 250 KB** las otras cuatro. Con eso pasás de 8 MB totales a menos de 1,4 MB. Squoosh (`squoosh.app`) lo hace en el navegador y es gratis.
3. **Formato:** dejalas en **PNG** — los nombres de archivo están cableados en `constants.php` con extensión `.png` y cambiar a WebP obliga a tocar código en cinco lugares. Un PNG bien comprimido entra holgado en el presupuesto de arriba.
4. **Componer el producto, si querés mostrarlo.** Las tablets y laptops de los prompts salen con pantalla apagada a propósito: si querés que se vea la interfaz, pegá encima un screenshot real ahora (§3, regla 3).

**Checklist antes de dar por buena cada imagen:**

- [ ] El EPP es correcto para esa tarea y ese lugar — **aprobado por alguien que conozca la operación real**, no por vos ni por mí
- [ ] No hay texto, ni logos, ni marcas legibles en ninguna parte
- [ ] Las manos tienen cinco dedos y se ven naturales
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
