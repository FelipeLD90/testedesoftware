<?php
/**
 * FilmeForm Registration
 * @author  <your name here>
 */
class FilmeFormAdvanced extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TForm('form_Filme');
                              
        // add a table inside form
        $table = new TTable;
        $table_genero = new TTable;
        $table_image = new TTable;
        
        $notebook = new TNotebook(500, 350);
        
        $this->form->add($notebook);
        
        $notebook->appendPage('Dados do Filme', $table);
        $notebook->appendPage('Generos do Filme', $table_genero);
        $notebook->appendPage('Imagem do Filme', $table_image);
        

        // create the form fields
        $emptyID                        = new THidden('id');
        $titulo                         = new TEntry('titulo');
        $pais                           = new TEntry('pais');
        $ano                            = new TEntry('ano');
        $dataVisualizacao               = new TDate('dataVisualizacao');
        $local                          = new TEntry('local');
        $compania                       = new TEntry('compania');
        $nota                           = new TSlider('nota');
        $comentario                     = new TEntry('comentario');
        $photo_path                     = new TFile('photo_path');


        // define the sizes
        $titulo->setSize(400);
        $titulo->setMaxLength(140);
        $pais->setSize(400);
        $pais->setMaxLength(140);
        $ano->setSize(100);
        $ano->setMask(9999);
        $dataVisualizacao->setSize(100);
        $local->setSize(400);
        $local->setMaxLength(140);
        $compania->setSize(400);
        $compania->setMaxLength(140);
        $nota->setSize(400);
        $nota->setRange(1, 5, 1);        
        $comentario->setSize(400);
        $comentario->setMaxLength(140);
        
        // define as mascaras
        $dataVisualizacao->setMask('dd/mm/yyyy');
        
        // validations
        $titulo->addValidation('Título', new TRequiredValidator);
        $titulo->addValidation('Título', new TMaxLengthValidator, array(140));
        $pais->addValidation('País', new TMaxLengthValidator, array(140));
        $ano->addValidation('Ano', new TMinValueValidatorNull, array(1900));
        $dataVisualizacao->addValidation('Data de Visualização', new TRequiredValidator);
      //  $dataVisualizacao->addValidation('Data de Visualização', new TDateValidator);
        $local->addValidation('Local', new TMaxLengthValidator, array(140));
        $compania->addValidation('Companhia', new TMaxLengthValidator, array(140));        
        $comentario->addValidation('Comentário', new TMaxLengthValidator, array(140));
        


        // add one row for each form field
        $table->addRowSet( $label_titulo = new TLabel('Título:'), $titulo );
        $label_titulo->setFontColor('#FF0000');
        $table->addRowSet( new TLabel('País:'), $pais );
        $table->addRowSet( new TLabel('Ano:'), $ano );
        $table->addRowSet( $label_dataVisualizacao = new TLabel('Data de Visualização:'), $dataVisualizacao );
        $label_dataVisualizacao->setFontColor('#FF0000');
        $table->addRowSet( new TLabel('Local:'), $local );
        $table->addRowSet( new TLabel('Companhia:'), $compania );
        $table->addRowSet( $label_nota = new TLabel('Nota:'), $nota );
        $label_nota->setFontColor('#FF0000');
        $table->addRowSet( new TLabel('Comentário:'), $comentario );
        
        $table->addRowSet( new TLabel(''), $emptyID );

        $genero_list = new TDBCheckGroup('genero_list', 'filme', 'Genero', 'id', 'descricao');
        $table_genero->addRow()->addCell($lbl=new TLabel('Escolha um ou mais Gêneros'));
        $table_genero->addRow()->addCell($genero_list);
        
        $photo_path->setSize(200);
              
        
        if(isset($_REQUEST['key'])){
            $nome = $_REQUEST['key'];         
            $html = new TImage('app/images/foto/'.$nome.'.png');
            $html->height=150;
            $html->width=90;
        } else {
            $html = new TLabel('');
        }
       
        $table_image->addRowSet( new TLabel('Imagem:'), $html );
        $table_image->addRowSet( new TLabel('Carregar:'), $photo_path );
        
        // validations
        $genero_list->addValidation('Gênero (pelo menos um)', new TRequiredValidator);
        
        $this->form->setFields(array($titulo,$pais,$ano,$dataVisualizacao,$local,$compania,$nota,$comentario,$emptyID, $genero_list,$photo_path));

        // create the form actions
        $save_button   = TButton::create('save', array($this, 'onSave'), _t('Save'), 'ico_save.png');
        $delete_button = TButton::create('delete', array($this, 'onDelete'), _t('Delete'), 'ico_delete.png');
        $new_button    = TButton::create('new',  array($this, 'onEdit'), _t('New'),  'ico_new.png');
        $list_button   = TButton::create('list', array('FilmeList', 'onReload'), _t('List'), 'ico_datagrid.png');
        
        $this->form->addField($save_button);
        $this->form->addField($delete_button);
        $this->form->addField($new_button);
        $this->form->addField($list_button);
        
        $subtable = new TTable;
        $row = $subtable->addRow();
        $row->addCell($save_button);
        $row->addCell($delete_button);
        $row->addCell($new_button);
        $row->addCell($list_button);
         
        $vbox = new TVBox;
        $vbox->add($this->form);
        $vbox->add($subtable);    
            
        parent::add($vbox);
    }

    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    function onSave()
    {
        try
        {
            TTransaction::open('filme'); // open a transaction
            
            // get the form data into an active record Filme
            $object = $this->form->getData('Filme');          
            
            $object->dataVisualizacao = $this->formatDate($object->dataVisualizacao);
            
            if($object->genero_list)
            {
                foreach($object->genero_list as $genero_id)
                {
                    $object->addGenero(new Genero($genero_id));
                }
            }
        
            $this->form->validate(); // form validation
            $object->store(); // stores the object
            $object->dataVisualizacao = $this->formatDateBR($object->dataVisualizacao);
            $this->form->setData($object); // keep form data
            
           // $this->upload($object->imagem, 'teste');
            TTransaction::close(); // close the transaction
            
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Erros encontrados:</b> <br />' . $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
        
        if ($object instanceof Filme)
        {
            $source_file   = 'tmp/'.$object->photo_path;
            $target_file   = 'app/images/foto/' . $object->id.'.png';
          
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            
            if (file_exists($source_file) AND ($finfo->file($source_file) == 'image/png' OR $finfo->file($source_file) == 'image/jpeg'))
            {
                // move to the target directory
                rename($source_file, $target_file);
               
            } 
            else
            {
                 if($object->photo_path)
                 {
                     new TMessage('error', '<b>Arquivo de imagem inálido</b> <br />'); // shows the exception error message
                 }                 
            }                    
        }
        
    }
    
    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                $key=$param['key'];  // get the parameter $key
                TTransaction::open('filme'); // open a transaction
                $object = new Filme($key); // instantiates the Active Record
                
                if(!$object->dataVisualizacao)
                {
                    $object->dataVisualizacao = $object->datavisualizacao;
                }
                
                $object->dataVisualizacao = $this->formatDateBR($object->dataVisualizacao);
                
                $generos = $object->getGeneros();
                $genero_list = array();
                if($generos)
                {
                    foreach ($generos as $genero)
                    {
                        $genero_list[] = $genero->id;
                    }
                }
                $object->genero_list = $genero_list;
                
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $object = new StdClass;
                $object->nota = 1;
                
                $this->form->clear();
                $this->form->setData($object); // fill the form
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
     /**
     * method onDelete()
     * executed whenever the user clicks at the delete button
     * Ask if the user really wants to delete the record
     */
    function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
     /**
     * method Delete()
     * Delete a record
     */
    function Delete($param)
    {
        try
        {
            $key=$param['id']; // get the parameter $key
            TTransaction::open('filme'); // open a transaction with database
            $object = new Filme($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            $object = new StdClass;
            $object->nota = 1;
            $this->form->clear();
            $this->form->setData($object); // fill the form
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted')); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    public function formatDate($date)
    {        
        $dt = explode('/', $date);
        $retorno = $dt[2].'-'.$dt[1].'-'.$dt[0];
        return $retorno;
    }

    public function formatDateBR($date)
    {        
        $dt = explode('-', $date);
        $retorno = $dt[2].'/'.$dt[1].'/'.$dt[0];
        return $retorno;
    }        
        
}
