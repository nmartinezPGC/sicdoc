<?php
/********************************************************
 * Description of DateFormatFunction
 * Funcion para dar Formato a las Fechas de los Querys 
 * Fecha: 2017-12-27 | 08:56 am.
 * @author Nahum Martinez <nmartinez.salgado@yahoo.com>
 ********************************************************/
namespace AppBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

class DateFormatFunction extends FunctionNode {
    public $date;
 
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
 
        $this->date = $parser->ArithmeticPrimary();
 
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
 
    public function getSql(SqlWalker $sqlWalker)
    {
        return "DATE(".$sqlWalker->walkArithmeticPrimary($this->date).")";
    }
}
