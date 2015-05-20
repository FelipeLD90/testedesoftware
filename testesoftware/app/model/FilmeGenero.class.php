<?php
/**
 * FilmeGenero Active Record
 * @author  <your-name-here>
 */
class FilmeGenero extends TRecord
{
    const TABLENAME = 'filme_genero';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('filme_id');
        parent::addAttribute('genero_id');
    }


}
