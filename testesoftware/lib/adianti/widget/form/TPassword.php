<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TForm;
use Adianti\Control\TAction;

use Exception;

/**
 * Password Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TPassword extends TField implements AdiantiWidgetInterface
{
    private $exitAction;
    protected $formName;
    
    /**
     * Define the action to be executed when the user leaves the form field
     * @param $action TAction object
     */
    function setExitAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->exitAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }
    
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        // define the tag properties
        $this->tag-> name  =  $this->name;   // tag name
        $this->tag-> value =  $this->value;  // tag value
        $this->tag-> type  =  'password';    // input type
        $this->setProperty('style', "width:{$this->size}px", FALSE); //aggregate style info
        
        // verify if the field is not editable
        if (parent::getEditable())
        {
            if (isset($this->exitAction))
            {
                if (!TForm::getFormByName($this->formName) instanceof TForm)
                {
                    throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
                }
                
                $string_action = $this->exitAction->serialize(FALSE);
                $this->setProperty('onBlur', "serialform=(\$('#{$this->formName}').serialize());
                                              __adianti_ajax_lookup('$string_action&'+serialform, this)");
            }
        }
        else
        {
            // make the field read-only
            $this->tag-> readonly = "1";
            $this->tag->{'class'} = 'tfield_disabled'; // CSS
        }
        
        // show the tag
        $this->tag->show();
    }
}
