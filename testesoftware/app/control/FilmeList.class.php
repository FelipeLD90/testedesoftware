<?php
/**
 * FilmeList Listing
 * @author  <your name here>
 */
class FilmeList extends TPage
{
    private $form;     // registration form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TForm('form_search_Filme');
        $this->form->class = 'tform'; // CSS class
        
        // creates a table
        $table = new TTable;
        $table-> width = '100%';
        $this->form->add($table);
        
        // add a row for the form title
        $row = $table->addRow();
        $row->class = 'tformtitle'; // CSS class
        $row->addCell( new TLabel('Busca de Filme') )->colspan = 2;
        

        // create the form fields
        $titulo                         = new TEntry('titulo');
        $dataVisualizacao               = new TDate('dataVisualizacao');
        $dataVisualizacaoFinal          = new TDate('dataFinal');
        
        $dataVisualizacao->setMask('dd/mm/yyyy');
        $dataVisualizacaoFinal->setMask('dd/mm/yyyy');
                
        // define the sizes
        $titulo->setSize(200);
        $dataVisualizacao->setSize(100);
        $dataVisualizacaoFinal->setSize(100);
               
        // add one row for each form field
        $table->addRowSet( new TLabel('Título:'), $titulo );
        $table->addRowSet( new TLabel('Data de Visualização Inicial:'), $dataVisualizacao );
        $table->addRowSet( new TLabel('Data de Visualização Final:'), $dataVisualizacaoFinal );
                 
        $this->form->setFields(array($titulo,$dataVisualizacao,$dataVisualizacaoFinal));


        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Filme_filter_data') );
        
        // create two action buttons to the form
        $find_button = TButton::create('find', array($this, 'onSearch'), _t('Find'), 'ico_find.png');
        $new_button  = TButton::create('new',  array('FilmeFormAdvanced', 'onEdit'), _t('New'), 'ico_new.png');
        $clean_button  = TButton::create('clean',  array('FilmeList', 'onClean'), 'Limpar', 'ico_close.png');
        
        $this->form->addField($find_button);
        $this->form->addField($new_button);
        $this->form->addField($clean_button);
        
        $buttons_box = new THBox;
        $buttons_box->add($find_button);
        $buttons_box->add($new_button);
        $buttons_box->add($clean_button);
        
        // add a row for the form action
        $row = $table->addRow();
        $row->class = 'tformaction'; // CSS class
        $row->addCell($buttons_box)->colspan = 2;
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(320);
        
        // creates the datagrid columns
        $imagem   = new TDataGridColumn('imagem', 'Capa', 'center', 50);
        $titulo   = new TDataGridColumn('titulo', 'Título', 'left', 200);
        $ano   = new TDataGridColumn('ano', 'Ano', 'right', 50);
        $dataVisualizacao   = new TDataGridColumn('dataVisualizacao', 'Data', 'left', 80);
        $dataVisualizacao->setTransformer(array($this, 'formatDate'));
        $nota   = new TDataGridColumn('nota', 'Nota', 'right', 30);
        $genero_list   = new TDataGridColumn('genero_list', 'Gêneros', 'left', 100);
       
        $genero_list->setTransformer(array($this, 'showGeneros'));
        $imagem->setTransformer(array($this, 'showImagem'));
       
        // add the columns to the DataGrid
        $this->datagrid->addColumn($imagem);
        $this->datagrid->addColumn($titulo);
        $this->datagrid->addColumn($ano);
        $this->datagrid->addColumn($dataVisualizacao);
        $this->datagrid->addColumn($nota);
        $this->datagrid->addColumn($genero_list);


        // creates the datagrid column actions
        $order_titulo= new TAction(array($this, 'onReload'));
        $order_titulo->setParameter('order', 'titulo');
        $titulo->setAction($order_titulo);

        $order_ano= new TAction(array($this, 'onReload'));
        $order_ano->setParameter('order', 'ano');
        $ano->setAction($order_ano);

        $order_dataVisualizacao= new TAction(array($this, 'onReload'));
        $order_dataVisualizacao->setParameter('order', 'dataVisualizacao');
        $dataVisualizacao->setAction($order_dataVisualizacao);

        $order_nota= new TAction(array($this, 'onReload'));
        $order_nota->setParameter('order', 'nota');
        $nota->setAction($order_nota);
       
        // creates two datagrid actions
        $action1 = new TDataGridAction(array('FilmeFormAdvanced', 'onEdit'));
        $action1->setLabel(_t('Edit'));
        $action1->setImage('ico_edit.png');
        $action1->setField('id');
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        $action2->setLabel(_t('Delete'));
        $action2->setImage('ico_delete.png');
        $action2->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // create the page container
        $container = TVBox::pack( $this->form, $this->datagrid, $this->pageNavigation);
        parent::add($container);
    }
    
    public function showGeneros($genero_list, $objeto, $row)
    {
        $generos = $objeto->getGeneros();
        if ($generos)
        {
            $genero_descricoes = array();
            foreach ($generos as $genero)
            {
                $genero_descricoes[] = $genero->descricao;
            }
            return implode(',', $genero_descricoes);
        }
    }
    
    public function showImagem($imagem, $object, $row)
    {
    
        $nome = $object->id;                
        //$imagem = new TLabel('<img src="./app/images/foto/'.$nome.'.png" height="42" width="42">');
        $imagem = new TImage('app/images/foto/'.$nome.'.png');
        $imagem->height=42;
        $imagem->width=42;
        return $imagem;
    }
    /**
     * method onInlineEdit()
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('filme'); // open a transaction with database
            $object = new Filme($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * method onSearch()
     * Register the filter in the session when the user performs a search
     */
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
               
        // clear session filters
        TSession::setValue('FilmeList_filter_titulo',   NULL);
        TSession::setValue('FilmeList_filter_ano',   NULL);
        TSession::setValue('FilmeList_filter_dataVisualizacao',   NULL);
        TSession::setValue('FilmeList_filter_nota',   NULL);
        TSession::setValue('FilmeList_filter_imagem',   NULL);


        if (isset($data->titulo) AND ($data->titulo)) {
            $filter = new TFilter('titulo', 'like', "%{$data->titulo}%"); // create the filter
            TSession::setValue('FilmeList_filter_titulo',   $filter); // stores the filter in the session
        }
  
          if ((isset($data->dataVisualizacao) AND ($data->dataVisualizacao)) OR (isset($data->dataFinal) AND ($data->dataFinal))) {
            if ((isset($data->dataVisualizacao) AND ($data->dataVisualizacao)) AND (isset($data->dataFinal) AND ($data->dataFinal))) {
                     		$data->dataVisualizacao = $this->formatDateUS($data->dataVisualizacao);
							$data->dataFinal = $this->formatDateUS($data->dataFinal);
                     if($data->dataFinal > $data->dataVisualizacao)
                     {
						 $filter = new TFilter('dataVisualizacao', 'BETWEEN', $data->dataVisualizacao, $data->dataFinal); // create the filter
						 TSession::setValue('FilmeList_filter_dataVisualizacao',   $filter); // stores the filter in the session	
						 $data->dataVisualizacao = $this->formatDateBR($data->dataVisualizacao);
						 $data->dataFinal = $this->formatDateBR($data->dataFinal);
			 
					 }
					 else
					 {
						 new TMessage('error', '<b>Data Final menor que a Data Inicial</b> '); // shows the exception error message 
					 }              
                } 
             elseif(isset($data->dataVisualizacao) AND ($data->dataVisualizacao)){
				 		$data->dataVisualizacao = $this->formatDateUS($data->dataVisualizacao);
		
				     $filter = new TFilter('dataVisualizacao', '>=', $data->dataVisualizacao); // create the filter
                     TSession::setValue('FilmeList_filter_dataVisualizacao',   $filter); // stores the filter in the session
                     $data->dataVisualizacao = $this->formatDateBR($data->dataVisualizacao);
			 }
			 else
			 {
				 $data->dataFinal = $this->formatDateUS($data->dataFinal);
				 $filter = new TFilter('dataVisualizacao', '<=', $data->dataFinal); // create the filter
                 TSession::setValue('FilmeList_filter_dataVisualizacao',   $filter); // stores the filter in the session
                 $data->dataFinal = $this->formatDateBR($data->dataFinal);
			 }           
        }
        
        
		
        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        
        TSession::setValue('Filme_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * method onReload()
     * Load the datagrid with the database objects
     */
    function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'filme'
            TTransaction::open('filme');
            
            // creates a repository for Filme
            $repository = new TRepository('Filme');
            $limit = 15;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'dataVisualizacao';  
            }
            
            $param['direction'] = 'desc';
            
            if($param['order'] == 'titulo')
            {
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('FilmeList_filter_titulo')) {
                $criteria->add(TSession::getValue('FilmeList_filter_titulo')); // add the session filter
            }
         
            if (TSession::getValue('FilmeList_filter_dataVisualizacao')) {
                $criteria->add(TSession::getValue('FilmeList_filter_dataVisualizacao')); // add the session filter
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria);
            
            $this->datagrid->clear(); 
                                   
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                    
                    $nome = null;
                    $imagem = null;
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
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
            $key=$param['key']; // get the parameter $key
            TTransaction::open('filme'); // open a transaction with database
            $object = new Filme($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            $this->onReload( $param ); // reload the listing
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted')); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * method show()
     * Shows the page
     */
    function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
    
    public function formatDate($date, $object)
    {        
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }
    
    public function formatDateUS($date)
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
            
    public function onClean()
    {
         TSession::setValue('FilmeList_filter_titulo',   ''); // stores the filter in the session
         TSession::setValue('FilmeList_filter_dataVisualizacao',   ''); // stores the filter in the session
         TSession::setValue('Filme_filter_data',   ''); // limpa dados de formulario
         
         $this->form->clear();

         $this->onReload( );
         
    }
}
