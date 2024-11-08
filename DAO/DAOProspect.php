<?php
namespace DAO;
mysqli_report(MYSQLI_REPORT_STRICT);
$separador = DIRECTORY_SEPARATOR;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once('../models/Prospect.php');

use models\Prospect;
/**
 * Esta classe é responsável por fazer a comunicação 
 * com o banco de dados, provendo os métodos de logar 
 * e incluir Prospect
 * @author Mateus Bazzei
 */
class DAOProspect{
    private function conectarDB(){
        $separator = DIRECTORY_SEPARATOR;
        $diretorioBase = dirname((__FILE__).$separator);
        require('configdb.php');

        try{
            $conn = new \MySQLi($dbhost, $user, $password, $banco);
            return $conn;
        }catch(mysqli_sql_exception $e){
            throw new \Exception($e);
            die;
        }
    }

    /**
     * Este método tem a função de validar os dados fornecidos
     * pelo usuário para logar no sistema.
     * @param string $login Login do prospect.
     * @param string $senha Senha do prospect.
     */
    public function logar($login, $senha){
        $conexaoDB = $this->conectarDB();
        $prospect = new Prospect();
        $sql = $conexaoDB->prepare('select codigo, nome, email, celular, facebook, whatsapp from prospect
                                    where
                                    login = ?
                                    and
                                    senha = ?');
        $sql->bind_param("ss", $login, $senha);
        $sql->execute();

        $resultado = $sql->get_result();

        if($resultado->num_rows === 0){
            $prospect->addProspect(null, null, null, null, FALSE);
        }else{
            while($linha = $resultado->fetch_assoc()){
                $prospect->addProspet($linha['login'], $linha['nome'],
                                    $linha['email'], $linha['celular'], $linha['facebook'], $linha['whatsapp'],
                                    TRUE);
            }
            $sql->close();
            $conexaoDB->close();
            return $prospect;
        }
    }
    public function incluirProspect($nome, $email, $login, $senha){
        $conexaoDB = $this->conectarDB();

        $sqlInsert = $conexaoDB->prepare("insert into prospect
                                        (nome, email, login, senha)
                                        values
                                        (?, ?, ?, ?)");
        $sqlInsert->bind_param("ssss", $nome, $email, $login, $senha);

        $sqlInsert->execute();

        if(!$sqlInsert->error){
            $retorno = TRUE;
        }else{
            throw new \Exception("Não foi possível incluir novo prospect");
            die;
        }
        $conexaoDB->close();
        $sqlInsert->close();
        return $retorno;
    }
}
?>
