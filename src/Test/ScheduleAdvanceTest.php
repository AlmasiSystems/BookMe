<?php
namespace IComeFromTheNet\BookMe\Test;

use DateTime;
use Doctrine\DBAL\Types\Type;
use IComeFromTheNet\BookMe\Test\Base\TestMgtBase;
use IComeFromTheNet\BookMe\Bus\Command\RefreshScheduleCommand;
use IComeFromTheNet\BookMe\Bus\Command\AssignRuleToScheduleCommand;
use IComeFromTheNet\BookMe\BookMeService;
use IComeFromTheNet\BookMe\Bus\Exception\ScheduleException;



class ScheduleAdvanceTest extends TestMgtBase
{
    
    
    protected $aDatabaseId = [];
    
    
    
   protected function handleEventPostFixtureRun()
   {
      // Create the Calendar 
      $oService = new BookMeService($this->getContainer());
      
      $oNow     = $this->getContainer()->getNow();
      
      
      $oStart = clone $oNow;
      $oStart->setDate($oNow->format('Y'),1,1);
      
      $oService->addCalenderYears(5,$oStart);
      
      // Timeslots
      
      $iFiveMinuteTimeslot    = $oService->addTimeslot(5);
      $iTenMinuteTimeslot     = $oService->addTimeslot(10);
      $iFifteenMinuteTimeslot = $oService->addTimeslot(15);

      $oService->toggleSlotAvability($iTenMinuteTimeslot);    
  
     // Teams
  
      $iMemberOne   = $oService->registerMembership();
      $iMemberTwo   = $oService->registerMembership();
      $iMemberThree = $oService->registerMembership();
      $iMemberFour  = $oService->registerMembership();
    
      $iTeamOne     = $oService->registerTeam($iFiveMinuteTimeslot);
      $iTeamTwo     = $oService->registerTeam($iFifteenMinuteTimeslot);
         
         
      // Schedules
      $iMemberOneSchedule   = $oService->startSchedule($iMemberOne,   $iFiveMinuteTimeslot, $oNow->format('Y'));
      $iMemberTwoSchedule   = $oService->startSchedule($iMemberTwo,   $iFiveMinuteTimeslot, $oNow->format('Y'));
      $iMemberThreeSchedule = $oService->startSchedule($iMemberThree, $iFiveMinuteTimeslot, $oNow->format('Y'));
      $iMemberFourSchedule  = $oService->startSchedule($iMemberFour,  $iFiveMinuteTimeslot, $oNow->format('Y'));
      
      // Rules Single
      $oSingleDate = clone $oNow;
      $oSingleDate->setDate($oNow->format('Y'),1,14);
        
      $oDayWorkDayRuleStart = clone $oNow;
      $oDayWorkDayRuleStart->setDate($oNow->format('Y'),1,1);
      
      $oDayWorkDayRuleEnd = clone $oNow;
      $oDayWorkDayRuleEnd->setDate($oNow->format('Y'),12,31);
      
      $oHolidayStart = clone $oNow;
      $oHolidayStart->setDate($oNow->format('Y'),8,7);
      
      $oHolidayEnd   = clone $oNow; 
      $oHolidayEnd->setDate($oNow->format('Y'),8,14);
      
      
      $iNineAmSlot = (12*9) *5;
      $iFivePmSlot = (12*17)*5;
      $iTenPmSlot  = (12*20)*5;    
        
      $iRepeatWorkDayRule    = $oService->createRepeatingWorkDayRule($oDayWorkDayRuleStart,$oDayWorkDayRuleEnd,$iFiveMinuteTimeslot,$iNineAmSlot,$iFivePmSlot,'1-5','*','2-12');
      $iSingleWorkDayRule    = $oService->createSingleWorkDayRule($oSingleDate,$iFiveMinuteTimeslot,$iFivePmSlot,$iTenPmSlot); 
      
      $iMidaySlot = (12*12)*5;
      $iOnePmSlot = (12*13)*5;
      
      $iEightPmSlot  = (12*18)*5;
      $iEightThirtyPmSlot = ((12*18) + 6)*5;
      
      $iRepeatBreakRule      = $oService->createRepeatingBreakRule($oDayWorkDayRuleStart,$oDayWorkDayRuleEnd,$iFiveMinuteTimeslot,$iMidaySlot,$iOnePmSlot,'1-5','*','2-12');
      $iSingleBreakRule      = $oService->createSingleBreakRule($oSingleDate,$iFiveMinuteTimeslot,$iEightPmSlot,$iEightThirtyPmSlot); 
            
            
      $iRepeatHolidayRule    = $oService->createRepeatingHolidayRule($oDayWorkDayRuleStart,$oDayWorkDayRuleEnd,$iFiveMinuteTimeslot,$iNineAmSlot,$iFivePmSlot,'*','28-30','*');    
      $iSingleBreakRule      = $oService->createSingleHolidayRule($oHolidayStart,$iFiveMinuteTimeslot,$iNineAmSlot,$iFivePmSlot);             
    
    
      $iRepeatOvertimeRule   = $oService->createRepeatingOvertimeRule($oDayWorkDayRuleStart,$oDayWorkDayRuleEnd,$iFiveMinuteTimeslot,$iNineAmSlot,$iFivePmSlot,'*','28-30','*');
      $iSingleOvertimeRule   = $oService->createSingleOvertmeRule($oHolidayStart,$iFiveMinuteTimeslot,$iNineAmSlot,$iFivePmSlot);
      
      
      // Link Rules to Schedule
      
            
      $this->aDatabaseId = [
        'five_minute'            => $iFiveMinuteTimeslot,
        'ten_minute'             => $iTenMinuteTimeslot,
        'fifteen_minute'         => $iFifteenMinuteTimeslot,
        'member_one'             => $iMemberOne,
        'member_two'             => $iMemberTwo,
        'member_three'           => $iMemberThree,
        'member_four'            => $iMemberFour,
        'team_two'               => $iTeamTwo,
        'team_one'               => $iTeamOne,
        'work_repeat'            => $iRepeatWorkDayRule,
        'work_single'            => $iSingleWorkDayRule,
        'break_repeat'           => $iRepeatBreakRule,
        'break_single'           => $iSingleBreakRule,
        'holiday_repeat'         => $iRepeatHolidayRule,
        'holiday_single'         => $iSingleBreakRule,
        'overtime_repeat'        => $iRepeatOvertimeRule,
        'overtime_single'        => $iSingleOvertimeRule,
        'schedule_member_single' => $iMemberOneSchedule,
        'schedule_member_two'    => $iMemberTwoSchedule,
        'schedule_member_three'  => $iMemberThreeSchedule,
        'schedule_member_four'   => $iMemberFourSchedule,
        
      ];
      
      
   }  
   
   
    /**
    * @group Management
    */ 
    public function testScheduleCommands()
    {
        $iRuleOneId        = $this->aDatabaseId['work_repeat'];
        $iRuleTwoId        = $this->aDatabaseId['holiday_repeat'];
        $iRuleThreeId      = $this->aDatabaseId['overtime_repeat']; 
        
        $iScheduleId    = $this->aDatabaseId['schedule_member_two'];
        
        $this->ApplyRulesTest($iScheduleId, $iRuleOneId,$iRuleTwoId,$iRuleThreeId);
        $this->RefreshScheduleTest($iScheduleId);
       
    }
    
    protected function ApplyRulesTest($iScheduleId, $iRuleOneId,$iRuleTwoId, $iRuleThreeId)
    {
        $oContainer  = $this->getContainer();
      
        $oCommand = new AssignRuleToScheduleCommand($iScheduleId, $iRuleOneId, true);
        
        $oContainer->getCommandBus()->handle($oCommand);
        
        $bRuleExists = (bool) $oContainer->getDatabase()->fetchColumn('SELECT 1 
                                                FROM bm_rule_schedule 
                                                WHERE schedule_id = ? 
                                                AND rule_id = ? 
                                                AND is_rollover = true',[$iScheduleId,$iRuleOneId],0);
        
        $this->assertTrue($bRuleExists,'Rule has not been linked to schedule');
        
        
        
        $oCommand = new AssignRuleToScheduleCommand($iScheduleId, $iRuleTwoId, true);
        
        $oContainer->getCommandBus()->handle($oCommand);
        
        $bRuleExists = (bool) $oContainer->getDatabase()->fetchColumn('SELECT 1 
                                                FROM bm_rule_schedule 
                                                WHERE schedule_id = ? 
                                                AND rule_id = ? 
                                                AND is_rollover = true',[$iScheduleId,$iRuleTwoId],0);
        
        $this->assertTrue($bRuleExists,'Rule has not been linked to schedule');
        
        
        
        
        $oCommand = new AssignRuleToScheduleCommand($iScheduleId, $iRuleThreeId, true);
        
        $oContainer->getCommandBus()->handle($oCommand);
        
        $bRuleExists = (bool) $oContainer->getDatabase()->fetchColumn('SELECT 1 
                                                FROM bm_rule_schedule 
                                                WHERE schedule_id = ? 
                                                AND rule_id = ? 
                                                AND is_rollover = true',[$iScheduleId,$iRuleThreeId],0);
        
        $this->assertTrue($bRuleExists,'Rule has not been linked to schedule');
    }
    
    
    protected function RefreshScheduleTest($iScheduleId)
    {
        $oContainer  = $this->getContainer();
        
        $oCommandBus = $oContainer->getCommandBus(); 
       
        $oCommand = new RefreshScheduleCommand($iScheduleId);
       
        $oContainer->getCommandBus()->handle($oCommand);
        
        $bScheduleSlotExists = (bool) $oContainer->getDatabase()->fetchColumn('SELECT count(*) 
                                                FROM bm_schedule_slot 
                                                WHERE schedule_id = ? 
                                                and is_available = true and is_excluded = true and is_override = true 
                                                ',[$iScheduleId],0);
        
        $this->assertTrue($bScheduleSlotExists,'Rule has not been linked to schedule');
    
        
    }
    
   
    
    
}
/* end of file */
