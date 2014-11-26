<?php
namespace IComeFromTheNet\BookMe\Builder;

use \DateTime;
use DBALGateway\Builder\BuilderInterface;
use IComeFromTheNet\BookMe\Entity\MemberEntity;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Connection;


/**
 * Maps a Member from our Membership database. 
 * 
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @since 1.0
 */ 
class MemberBuilder extends AbstractBuilder implements BuilderInterface
{
    
    protected $schema;
    
    
    /*
    * Returns a doctrine DBAL type map
    *
    * @access protected
    * @return array[Doctrine\DBAL\Types\Type]
    */
    protected function getSchema() 
    {
        if(true === empty($this->schema)) {
            $this->schema = array(
                'registered_date' => Type::getType(Type::DATETIME),
                'membership_id'  =>  Type::getType(Type::INTEGER)
            );
        }
        return $this->schema;
    }
    
    
    /**
     * Class Constrcutor
     * 
     * @access public
     * @param Connection $conn the doctrine dbal adapter
     */ 
    public function __construct(Connection $conn)
    {
        parent::__construct($conn);
    }
    
    
    /**
      *  Convert data array into entity
      *
      *  @return mixed
      *  @param array $data
      *  @access public
      */
    public function build($data)
    {
        $entity = new MemberEntity();
        
        $this->convertToPHP($data);
        
        foreach($data as $key => $value) {
            
            switch($key) {
                case 'registered_date':
                      $entity->setCreatedDate($value);
                break;
                case 'membership_id':
                     $entity->setMemberID($value);    
                break;    
            }
        }

        return $entity;
    }

    /**
      *  Convert and entity into a data array
      *
      *  @return array
      *  @access public
      */
    public function demolish($entity)
    {

    }
    
}
/* End of Class */