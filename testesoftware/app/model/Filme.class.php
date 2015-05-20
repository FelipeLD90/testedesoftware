<?php
/**
 * Filme Active Record
 * @author  <your-name-here>
 */
class Filme extends TRecord
{
    const TABLENAME = 'filme';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $generos;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('titulo');
        parent::addAttribute('pais');
        parent::addAttribute('ano');
        parent::addAttribute('dataVisualizacao');
        parent::addAttribute('local');
        parent::addAttribute('compania');
        parent::addAttribute('nota');
        parent::addAttribute('comentario');
        parent::addAttribute('imagem');
    }

    
    /**
     * Method addGenero
     * Add a Genero to the Filme
     * @param $object Instance of Genero
     */
    public function addGenero(Genero $object)
    {
        $this->generos[] = $object;
    }
    
    /**
     * Method getGeneros
     * Return the Filme' Genero's
     * @return Collection of Genero
     */
    public function getGeneros()
    {
        return $this->generos;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->generos = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
    
        // load the related Genero objects
        $repository = new TRepository('FilmeGenero');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('filme_id', '=', $id));
        $filme_generos = $repository->load($criteria);
        if ($filme_generos)
        {
            foreach ($filme_generos as $filme_genero)
            {
                $genero = new Genero( $filme_genero->genero_id );
                $this->addGenero($genero);
            }
        }
    
        // load the object itself
        return parent::load($id);
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();
    
        // delete the related FilmeGenero objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('filme_id', '=', $this->id));
        $repository = new TRepository('FilmeGenero');
        $repository->delete($criteria);
        // store the related FilmeGenero objects
        if ($this->generos)
        {
            foreach ($this->generos as $genero)
            {
                $filme_genero = new FilmeGenero;
                $filme_genero->genero_id = $genero->id;
                $filme_genero->filme_id = $this->id;
                $filme_genero->store();
            }
        }
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        // delete the related FilmeGenero objects
        $repository = new TRepository('FilmeGenero');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('filme_id', '=', $id));
        $repository->delete($criteria);
        
    
        // delete the object itself
        parent::delete($id);
    }


}
