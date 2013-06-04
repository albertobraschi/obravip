<?php

class Wyomind_Simplegoogleshopping_Model_Observer
{	
	
    /**
     * Cronjob expression configuration
     */
   
   public function scheduledGenerateCatalogs($schedule)
    {
        $errors = array();
        $report ="*** Main cron job ***"."\n\n";
        $report.='Created at : '.$schedule->getCreatedAt()."\n";
        $report.='Scheduled at : '.$schedule->getScheduledAt()."\n";
        $report.='Executed at : '.date('Y-m-d h:i:s',time())."\n";
        $report.="-------------\n\n";

        $collection = Mage::getModel('simplegoogleshopping/simplegoogleshopping')->getCollection();
        
     
      
        foreach ($collection as $datafeed) {
               
               
                $report.="Data feed : ".$datafeed->getSimplegoogleshoppingFilename().' ('.$datafeed->getSimplegoogleshoppingId().')'."\n";;
                $report.="Cron expression : ".$datafeed->getCronExpr()."\n";;
                $report.="Execution started at : ".date('Y-m-d h:i:s',time())."\n";;
		
            try {
                $cron=(Mage::getModel('cron/schedule')->setCronExpr($datafeed->getCronExpr())->trySchedule(time()));   
                if($cron)$datafeed->generateXml();
                $report.="Status : ".(($cron)?"executed":"skipped")."\n";;
                $report.="Execution finished at : ".date('Y-m-d h:i:s',time())."\n";;
            }
            catch (Exception $e) {
                $errors[] = $e->getMessage();
                $report.="Error : ".($e->getMessage())."\n";
            }
              $report.="-------------\n\n";
        } 
        
        
         $report.="-------------\n\n";
        $report.='Finished at : '.date('Y-m-d h:i:s',time())."\n";
        
        if(Mage::getStoreConfig("simplegoogleshopping/setting/enable_report")){
            foreach(explode(',',Mage::getStoreConfig("simplegoogleshopping/setting/emails")) as $email){
               try{
                 
                   mail($email,Mage::getStoreConfig("simplegoogleshopping/setting/report_title"),$report);
                       
               }
               catch (Exception $e) {
                $errors[] = $e->getMessage();
                }
            }
            
        };
        $report.="\n\n\n*******************************************************\n\n\n";
        
       if(Mage::getStoreConfig("simplegoogleshopping/setting/report_debug")) echo nl2br($report);

    }
}