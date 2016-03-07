;<?php /*
;-------------------------------
;         APLICACION
;-------------------------------
; Configuracion de Aplicaciones
; app_activated: Es el ambiente activo, ej: production_app 
; default_language: Indica el lenguaje por defecto que es leido desde app/locale/{lenguaje}/

[APP]
app_activated = development_app_mysql
mod_rewrite = On
db_pwd_encripted = Off
default_language = es
utf8_encode = On


;-------------------------------------------------
;         AMBIENTES: desarrollo, produccion, otro.
;-------------------------------------------------
; database_mode: Es el entorno del a base de datos, 
;                configurado en app/conf/database.ini 
; interactive:   Habilita los mensajes de errores tecnicos, excepciones e indicaciones
;                para el programador (Activelo para desarrollo). 
; www_server:    Url Base del proyecto.
; debug:         Habilita librerias de debug (Activelo para desarrollo).                


[development_app_mysql]
database_mode = development
www_server = http://localhost/freekore/
interactive = On
debug = On
on_internet = Off
server_os = windows

[development_app_oracle]
database_mode = development_oracle
www_server = http://localhost/FREEKORE-CODE/trunk/
interactive = On
debug = On
on_internet = Off
server_os = windows

[development_app_firebird]
database_mode = development_firebird_sae
www_server = http://localhost/freekore-code/trunk/
interactive = On
debug = On
on_internet = Off
server_os = windows

[production_app]
database_mode = production
www_server = http://www.my-server.com/
interactive = Off
debug = Off
on_internet = On
server_os = linux

[test_app]
database_mode = production
www_server = http://www.my-server.com/
interactive = Off
debug = Off
on_internet = On
server_os = linux



; */ ?>