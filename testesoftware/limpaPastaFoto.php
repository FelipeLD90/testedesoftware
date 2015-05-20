<?php

echo 'inicio'."<br>";

$pasta_fotos = "app/images/foto";

if(is_dir($pasta_fotos))
{
$diretorio_fotos = dir($pasta_fotos);

while($arquivo_fotos = $diretorio_fotos->read())
{
if(($arquivo_fotos != '.') && ($arquivo_fotos != '..'))
{
unlink($pasta_fotos.'/'.$arquivo_fotos);
echo 'Arquivo '.$arquivo_fotos.' foi apagado com sucesso. <br />';
}
}

$diretorio_fotos->close();
}

echo 'fim';
?>
