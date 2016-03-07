-- 
-- 
-- Estructura de tabla para la tabla `comentarios`
-- 

CREATE TABLE `comentarios` (
  `id_comentario` int(11) NOT NULL auto_increment,
  `codigo_tabla` varchar(50) NOT NULL,
  `id_val_tabla` varchar(20) NOT NULL,
  `comentario` text NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `web_site` varchar(100) NOT NULL,
  `fec_reg` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_comentario`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  ;