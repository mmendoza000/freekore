<?php
class youtube
    {
        public $url;
        public $id;
        
        public function url2id()
        {
            $aux = explode("?",$this->url);
            $aux2 = explode("&",$aux[1]);            
            foreach($aux2 as $campo => $valor)
            {
                $aux3 = explode("=",$valor);
                if($aux3[0] == 'v') $video = $aux3[1];
            }
            return $this->id = $video;
        }
        
        public function url2id_($url)
        {
            $aux = explode("?",$url);
            $aux2 = explode("&",$aux[1]);            
            foreach($aux2 as $campo => $valor)
            {
                $aux3 = explode("=",$valor);
                if($aux3[0] == 'v') $video = $aux3[1];
            }
            return $this->id = $video;
        }
        
        public function thumb_url($tamanho=NULL)
        {
            $tamanho = $tamanho == "maior"?"hq":"";                
            $this->url2id();
            return 'http://i1.ytimg.com/vi/'.$this->id.'/'.$tamanho.'default.jpg';
        }
        
        public function thumb($tamanho=NULL)
        {
            $tamanho = $tamanho == "maior"?"hq":"";
            $this->url2id();    
            return '<img src="http://i1.ytimg.com/vi/'.$this->id.'/'.$tamanho.'default.jpg">';            
        }
        
        public function info() {
            $feedURL = 'http://gdata.youtube.com/feeds/base/videos?q='.$this->id.'&client=ytapi-youtube-search&v=2';    
            $sxml = simplexml_load_file($feedURL);                        
            foreach ($sxml->entry as $entry) {
                $details = $entry->content;    
                $info["titulo"] = $entry->title;
            }
            $details_notags = strip_tags($details);
            $texto = explode("From",$details_notags);
            $info["descripcion"] = $texto[0];
            $aux = explode("Views:",$texto[1]);
            $aux2 = explode(" ",$aux[1]);
            $info["views"] = $aux2[0];
            
            $aux = explode("Time:",$texto[1]);
            $aux2 = explode("More",$aux[1]);
            $info["tiempo"] = $aux2[0];
            
            $imgs = strip_tags($details,'<img>');
            $aux = explode("<img",$imgs);
            array_shift($aux);
            array_shift($aux);
            $aux2 = explode("gif\">",$aux[4]);
            array_pop($aux);
            $aux3 = $aux2[0].'gif">';
            $aux[] = $aux3;
            $stars = '';
            foreach($aux as $campo => $valor) {
                $stars .= '<img'.$valor;
            }
            $info["estrellas"] = $stars;
            
            $info['thumbnail'] = 'http://img.youtube.com/vi/'.$this->id.'/2.jpg';
            
            return $info;
        }
        
        public function busca($palavra) {
            $feedURL = 'http://gdata.youtube.com/feeds/base/videos?q='.$palavra.'&client=ytapi-youtube-search&v=2';    
            $sxml = simplexml_load_file($feedURL);    
            $i=0;
            foreach ($sxml->entry as $entry) {
                $details = $entry->content;    
                $info[$i]["titulo"] = $entry->title;    
                $aux = explode($info[$i]["titulo"],$details);            
                $aux2 = explode("<a",$aux[0]);                
                $aux3 = explode('href="',$aux2[1]);
                $aux4 = explode('&',$aux3[1]);
                $info[$i]["link"] = $aux4[0];
                $details_notags = strip_tags($details);
                $texto = explode("From",$details_notags);
                $info[$i]["descricao"] = $texto[0];
                $aux = explode("Views:",$texto[1]);
                $aux2 = explode(" ",$aux[1]);
                $info[$i]["views"] = $aux2[0];
                
                $aux = explode("Time:",$texto[1]);
                $aux2 = explode("More",$aux[1]);
                $info[$i]["tempo"] = $aux2[0];
                
                $imgs = strip_tags($details,'<img>');
                $aux = explode("<img",$imgs);
                array_shift($aux);
                array_shift($aux);
                $aux2 = explode("gif\">",$aux[4]);
                array_pop($aux);
                $aux3 = $aux2[0].'gif">';
                $aux[] = $aux3;
                $imagens = '';
                foreach($aux as $campo => $valor) {
                    $imagens .= '<img'.$valor;
                }
                $info[$i]["estrelas"] = $imagens;
                $i++;
            }
            return $info;
        }
        
        public function player($width,$height) {
            $this->url2id();
            print '<object width="'.$width.'" height="'.$height.'"><param name="movie" value="http://www.youtube.com/v/'.$this->id.'&fs=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$this->id.'&fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="'.$width.'" height="'.$height.'"></embed></object>';    
        }
    }
?>