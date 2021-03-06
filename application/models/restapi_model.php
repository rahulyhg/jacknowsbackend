<?php
if ( !defined( "BASEPATH" ) )
exit( "No direct script access allowed" );
class restapi_model extends CI_Model
{
    public function registeruser($name, $email, $password,$isexpert)
    {
        $newdata=0;
        $password=md5($password);
        //echo $email;
        $query=$this->db->query("SELECT `id` FROM `user` WHERE `email`='$email'");
				$num=$query->num_rows();

        if($num == 0)
        {
             $this->db->query("INSERT INTO `user`(`name`, `email`, `password`,`isexpert`,`status`,`accesslevel`) VALUE('$name','$email','$password','$isexpert','1','4')");
            $user=$this->db->insert_id();
//            $newdata = array(
//                    'id' => $user,
//                    'email' => $email,
//                    'name' => $name,
//                    'logged_in' => 'true'
//            );
            $newdata=$this->db->query("SELECT `id`, `name`, `password`, `email`, `accesslevel`, `timestamp`, `status`, `image`, `username`, `socialid`, `logintype`, `json`, `dob`, `street`, `address`, `city`, `state`, `country`, `pincode`, `facebook`, `google`, `twitter`, `firstname`, `lastname`, `maidenname`, `type`, `shortspecialities`, `interests`, `honorsawards`, `wallet`, `access`, `contact`, `percent`, `ameturetype`, `ametureprice`, `professionalprice`,`isexpert` FROM `user` WHERE `id`=$user")->row();
            $this->session->set_userdata($newdata);
        }
        else
        {
            $newdata=false;
				}
        return $newdata;
    } 
    public function searchExpert($expertname)
    {   
        $query=$this->db->query("SELECT `id`, `name`, `password`, `email`, `accesslevel`, `timestamp`, `status`, `image`, `username`, `socialid`, `logintype`, `json`, `dob`, `street`, `address`, `city`, `state`, `country`, `pincode`, `facebook`, `google`, `twitter`, `firstname`, `lastname`, `maidenname`, `type`, `shortspecialities`, `interests`, `honorsawards`, `wallet`, `access`, `contact`, `percent`, `ameturetype`, `ametureprice`, `professionalprice`, `gender`, `twittersocial`, `youtubesocial`, `facebooksocial`, `isexpert`,`hobbyverification`,`professionverification` FROM `user` WHERE ((`name` LIKE '%$expertname%') OR (`firstname` LIKE '%$expertname%') OR (`lastname` LIKE '%$expertname%')) AND (`isexpert`=1)")->result();
//       foreach($query as $row){
//           
//           $id=$row->id;
//           $userdetails=$this->restapi_model->getUserDetails($id);
//           
//       }
        if(!$query){
            return false;
        }
        else{
            return $query;
        }
        
    }
    
      function loginuser($email,$password)
    {
        $password=md5($password);
        $query=$this->db->query("SELECT `id`,`name` FROM `user` WHERE `email`='$email' AND `password`= '$password'");
        if($query->num_rows > 0)
        {
            $user=$query->row();
            $userid=$user->id;
            $name=$user->name;

$newdata=$this->db->query("SELECT `id`, `name`, `password`, `email`, `accesslevel`, `timestamp`, `status`, `image`, `username`, `socialid`, `logintype`, `json`, `dob`, `street`, `address`, `city`, `state`, `country`, `pincode`, `facebook`, `google`, `twitter`, `firstname`, `lastname`, `maidenname`, `type`, `shortspecialities`, `interests`, `honorsawards`, `wallet`, `access`, `contact`, `percent`, `ameturetype`, `ametureprice`, `professionalprice` FROM `user` WHERE `id`=$userid")->row();
//            $newdata = array(
//                'email'     => $email,
//                'name'     => $name,
//                'logged_in' => 'true',
//                'id'=> $userid
//            );

            $this->session->set_userdata($newdata);

            return $newdata;
        }
        else
        return false;
    }
    
    public function editPersonalDetails($id,$firstname, $lastname, $email,$gender,$address,$country,$state,$city,$pincode,$twittersocial,$youtubesocial,$facebooksocial,$contact,$isexpert,$image,$dob)
	{
        
		$data  = array(
			'firstname' => $firstname,
			'lastname' => $lastname,
			'email' => $email,
			'gender' => $gender,
            'address'=> $address,
            'country'=> $country,
            'state'=> $state,
            'city'=> $city,
            'pincode'=> $pincode,
            'twittersocial'=>$twittersocial,
            'youtubesocial'=>$youtubesocial,
            'facebooksocial'=>$facebooksocial,
            'contact'=>$contact,
            'isexpert'=>$isexpert,
            'image'=>$image,
            'status'=>1,
            'accesslevel'=>4,
            'dob'=>$dob
		);
		$this->db->where( 'id', $id );
		$query=$this->db->update( 'user', $data );
        
        $userdetails=$this->restapi_model->getUserDetails($id);
        
        if($query)
		return $userdetails;
        else
        return 0;
	}
    
    public function editProfessionDetails($id,$awards, $qualification, $experience,$websites,$videos,$description,$category,$photos,$skills){
        
        $querydelete=$this->db->query("DELETE FROM `expert_profession` WHERE `user`='$id'");
        $querydelete1=$this->db->query("DELETE FROM `expert_professionaward` WHERE `user`='$id'");
        $querydelete2=$this->db->query("DELETE FROM `expert_professioneducation` WHERE `user`='$id'");
        $querydelete3=$this->db->query("DELETE FROM `expert_professionexperience` WHERE `user`='$id'");
        $querydelete4=$this->db->query("DELETE FROM `expert_professionphoto` WHERE `user`='$id'");
        $querydelete5=$this->db->query("DELETE FROM `expert_professionskill` WHERE `user`='$id'");
        $querydelete6=$this->db->query("DELETE FROM `expert_professionvideolink` WHERE `user`='$id'");
        $querydelete7=$this->db->query("DELETE FROM `expert_professionwebsite` WHERE `user`='$id'");
        
        $query=$this->db->query("SELECT * FROM `expert_category` WHERE `name` LIKE '$category'")->row();
        $categoryid=$query->id;
//        
//        // Hoby
//        
        $this->db->query("INSERT INTO `expert_profession`( `user`, `category`,`description`) VALUE('$id','$category','$description')");
        $professionid=$this->db->insert_id();
        
        // AWARDS
        for($i=0; $i<count($awards); $i++){
         $data=array("user" => $id,"profession" => $professionid,"award" => $awards[$i]['awards']);
        $query=$this->db->insert( "expert_professionaward", $data );
        $awardid=$this->db->insert_id();
            
        } 
        // SKILLS
        for($i=0; $i<count($skills); $i++){
         $data=array("user" => $id,"profession" => $professionid,"skills" => $skills[$i]['text']);
        $query=$this->db->insert( "expert_professionskill", $data );
        $skillid=$this->db->insert_id();
            
        }

        
//        //QUALIFICATION
          for($i=0; $i<count($qualification); $i++){
         $data=array("user" => $id,"profession" => $professionid,"degree" => $qualification[$i]['degree'],"institute" => $qualification[$i]['institute'],"yearofpassing" => $qualification[$i]['year']);
        $query=$this->db->insert( "expert_professioneducation", $data );
        $qualificationid=$this->db->insert_id();
            
        }

        
//        // WORK EXPERIENCE
        
         for($i=0; $i<count($experience); $i++){
             $date = date_create( $experience[$i]['startdate']);
             $experience[$i]['startdate']=date_format($date, 'Y-m-d');
             
             $date = date_create( $experience[$i]['enddate']);
             $experience[$i]['enddate']=date_format($date, 'Y-m-d');
             
             
         $data=array("user" => $id,"profession" => $professionid,"companyname" => $experience[$i]['companyname'],"jobtitle" => $experience[$i]['jobtitle'],"companylogo" => $experience[$i]['companylogo'],"jobdescription" => $experience[$i]['jobdesc'],"startdate" => $experience[$i]['startdate'],"enddate" => $experience[$i]['enddate']);
        $query=$this->db->insert( "expert_professionexperience", $data );
        $qualificationid=$this->db->insert_id();
            
        }
        for($i=0; $i<count($photos); $i++){
         $data=array("user" => $id,"profession" => $professionid,"image" => $photos[$i]);
        $query=$this->db->insert( "expert_professionphoto", $data );
        $photoid=$this->db->insert_id();
            
        }
        
         // VIDEOLINKS
        
        for($i=0; $i<count($videos); $i++){
         $data=array("user" => $id,"profession" => $professionid,"videolink" => $videos[$i]['videos']);
        $query=$this->db->insert( "expert_professionvideolink", $data );
        $videosid=$this->db->insert_id();
            
        }

        // WEBSITE
            
         for($i=0; $i<count($websites); $i++){
         $data=array("user" => $id,"profession" => $professionid,"website" => $websites[$i]['websites']);
        $query=$this->db->insert( "expert_professionwebsite", $data );
        $videosid=$this->db->insert_id();
            
        }
            
        $userdetails=$this->restapi_model->getUserDetails($id);
        
		return $userdetails;
        
        
    }
    
    
    public function editHobbyDetails($id,$awards, $qualification,$websites,$videos,$description,$category,$skills,$yoexp,$photos){
        
        $querydelete=$this->db->query("DELETE FROM `expert_hobby` WHERE `user`='$id'");
        $querydelete1=$this->db->query("DELETE FROM `expert_hobbyawards` WHERE `user`='$id'");
        $querydelete2=$this->db->query("DELETE FROM `expert_hobbyeducation` WHERE `user`='$id'");
        $querydelete3=$this->db->query("DELETE FROM `expert_hobbyphotos` WHERE `user`='$id'");
        $querydelete4=$this->db->query("DELETE FROM `expert_hobbyskill` WHERE `user`='$id'");
        $querydelete5=$this->db->query("DELETE FROM `expert_hobbyvideolinks` WHERE `user`='$id'");
        $querydelete6=$this->db->query("DELETE FROM `expert_hobbywebsite` WHERE `user`='$id'");
        
        
        $query=$this->db->query("SELECT * FROM `expert_category` WHERE `name` LIKE '$category'")->row();
        $categoryid=$query->id;
//        
//        // HOBBY
//        
        $this->db->query("INSERT INTO `expert_hobby`( `user`, `category`,`description`,`expinyrs`) VALUE('$id','$category','$description','$yoexp')");
        $hobbyid=$this->db->insert_id();
        
        // Skills
        for($i=0; $i<count($skills); $i++){
         $data=array("user" => $id,"hobby" => $hobbyid,"skills" => $skills[$i]['text']);
        $query=$this->db->insert( "expert_hobbyskill", $data );
        $skillsid=$this->db->insert_id();
            
        }
        
        // AWARDS
        for($i=0; $i<count($awards); $i++){
         $data=array("user" => $id,"hobby" => $hobbyid,"awards" => $awards[$i]['awards']);
        $query=$this->db->insert( "expert_hobbyawards", $data );
        $awardid=$this->db->insert_id();
            
        }

        
//        //QUALIFICATION
          for($i=0; $i<count($qualification); $i++){
         $data=array("user" => $id,"hobby" => $hobbyid,"degree" => $qualification[$i]['degree'],"institute" => $qualification[$i]['institute'],"yearofpassing" => $qualification[$i]['year']);
        $query=$this->db->insert( "expert_hobbyeducation", $data );
        $educationid=$this->db->insert_id();
            
        }

    
        for($i=0; $i<count($photos); $i++){
         $data=array("user" => $id,"hobby" => $hobbyid,"image" => $photos[$i]);
        $query=$this->db->insert( "expert_hobbyphotos", $data );
        $photoid=$this->db->insert_id();
            
        }
        
         // VIDEOLINKS
        
        for($i=0; $i<count($videos); $i++){
         $data=array("user" => $id,"hobby" => $hobbyid,"videolink" => $videos[$i]['videos']);
        $query=$this->db->insert( "expert_hobbyvideolinks", $data );
        $videosid=$this->db->insert_id();
            
        }

        // WEBSITE
            
         for($i=0; $i<count($websites); $i++){
         $data=array("user" => $id,"hobby" => $hobbyid,"website" => $websites[$i]['websites']);
        $query=$this->db->insert( "expert_hobbywebsite", $data );
        $websitesid=$this->db->insert_id();
            
        }

        $userdetails=$this->restapi_model->getUserDetails($id);
        
		return $userdetails;
        
    }
    
    public function getUserDetails($id){
        $query['user']=$this->db->query("SELECT `id`, `name`, `email`, `accesslevel`, `timestamp`, `status`, `image`, `username`, `socialid`, `logintype`, `json`, `dob`, `street`, `address`, `city`, `state`, `country`, `pincode`, `facebook`, `google`, `twitter`, `firstname`, `lastname`, `maidenname`, `type`, `shortspecialities`, `interests`, `honorsawards`, `wallet`, `access`, `contact`, `percent`, `ameturetype`, `ametureprice`, `professionalprice`, `gender`, `twittersocial`, `youtubesocial`, `facebooksocial`,`isexpert`,`professionverification`,`hobbyverification` FROM `user` WHERE `id`='$id'")->row();
        
         $query['profession']=$this->db->query("SELECT `expert_profession`.`id`, `expert_profession`.`user`, `expert_profession`.`category` as `categoryid`, `expert_profession`.`description`,`expert_category`.`name` as `category`  FROM `expert_profession` LEFT OUTER JOIN `expert_category` ON `expert_category`.`id`=`expert_profession`.`category` WHERE `expert_profession`.`user`='$id'")->row();
        $professionid=$query['profession']->id;
         $query['profession']->awards=$this->db->query("SELECT `id`, `user`, `profession`, `award` as `awards` FROM `expert_professionaward` WHERE `user`='$id' AND `profession`='$professionid'")->result();
        
        $query['profession']->experience=$this->db->query("SELECT `id`, `profession`, `user`, `companyname`, `jobtitle`, `companylogo`, `jobdescription`as `jobdesc`, `startdate`, `enddate` FROM `expert_professionexperience` WHERE `user`='$id' AND `profession`='$professionid'")->result();
        
        $query['profession']->qualification=$this->db->query("SELECT `id`, `user`, `profession`, `degree`, `institute`, `yearofpassing` as `year` FROM `expert_professioneducation` WHERE `user`='$id' AND `profession`='$professionid'")->result(); 
        
        $query['profession']->skills=$this->db->query("SELECT `id`, `user`, `profession`, `skills` as `text` FROM `expert_professionskill` WHERE `user`='$id' AND `profession`='$professionid'")->result();
        
        $query['profession']->videos=$this->db->query("SELECT `id`, `user`, `profession`, `videolink` as `videos` FROM `expert_professionvideolink` WHERE `user`='$id' AND `profession`='$professionid'")->result();
        
        $query['profession']->websites=$this->db->query("SELECT `id`, `user`, `profession`, `website` as `websites` FROM `expert_professionwebsite` WHERE `user`='$id' AND `profession`='$professionid'")->result(); 
        
        $query['profession']->photos=$this->db->query("SELECT group_concat(`expert_professionphoto`.`image` separator ',') as `image` FROM `expert_professionphoto` WHERE `expert_professionphoto`.`user`='$id' AND `expert_professionphoto`.`profession`='$professionid' GROUP BY `expert_professionphoto`.`id`")->result(); 
//        SELECT (SELECT GROUP_CONCAT( cols.column_name) FROM (SELECT column_name FROM information_schema.columns WHERE table_name='test_table') as cols) FROM test_table
        
        
        //HOBBY
        
        
        
        $query['hobby']=$this->db->query("SELECT `expert_hobby`.`id`, `expert_hobby`.`user`, `expert_hobby`.`category` as `categoryid`, `expert_hobby`.`expinyrs` as `yoexp`, `expert_hobby`.`description`, `expert_hobby`.`skills`,`expert_category`.`name` as `category` FROM `expert_hobby` LEFT OUTER JOIN `expert_category` ON `expert_category`.`id`=`expert_hobby`.`category` WHERE `expert_hobby`.`user`='$id'")->row();
        $hobbyid=$query['hobby']->id;
         $query['hobby']->awards=$this->db->query("SELECT `id`, `user`, `hobby`, `awards` FROM `expert_hobbyawards` WHERE `user`='$id' AND `hobby`='$hobbyid'")->result();
        
        
        $query['hobby']->qualification=$this->db->query("SELECT `id`, `user`, `hobby`, `degree`, `institute`, `yearofpassing` as `year` FROM `expert_hobbyeducation` WHERE `user`='$id' AND `hobby`='$hobbyid'")->result(); 
        
        $query['hobby']->skills=$this->db->query("SELECT `id`, `user`, `hobby`, `skills` as `text` FROM `expert_hobbyskill` WHERE `user`='$id' AND `hobby`='$hobbyid'")->result();
        
        $query['hobby']->videos=$this->db->query("SELECT `id`, `user`, `hobby`, `videolink` as `videos` FROM `expert_hobbyvideolinks` WHERE `user`='$id' AND `hobby`='$hobbyid'")->result();
        
        $query['hobby']->websites=$this->db->query("SELECT `id`, `user`, `hobby`, `website` as `websites` FROM `expert_hobbywebsite` WHERE `user`='$id' AND `hobby`='$hobbyid'")->result(); 
        
        $query['hobby']->photos=$this->db->query("SELECT `id`, `user`, `hobby`, `image` FROM `expert_hobbyphotos` WHERE `user`='$id' AND `hobby`='$hobbyid'")->result(); 
        
        return $query;
   
        
    }
    
    public function editHobbyVerification($id,$hobbyvalue)
    {
        $data  = array(
            'hobbyverification' => $hobbyvalue
        );
        $this->db->where( 'id', $id );
        $query=$this->db->update( 'user', $data );
        
        if($query)
            return true;
        else
            return false;
    }

    public function editProfessionVerification($id,$profvalue)
    {
        $data  = array(
            'professionverification' => $profvalue
        );
        $this->db->where( 'id', $id );
        $query=$this->db->update( 'user', $data );
        
        if($query)
            return true;
        else
            return false;
    }
    public function askQuestion($fromuser, $question, $touser)
    {
        $this->db->query("INSERT INTO `expert_question`(`fromuser`, `question`) VALUE('$fromuser','$question')");
            $questionid=$this->db->insert_id();
        
        for($i=0 ;$i<count($touser); $i++){
            $this->db->query("INSERT INTO `expert_questionuser`(`question`, `touser`,`status`) VALUE('$questionid','$touser[$i]',2)");
            $questionuserid=$this->db->insert_id();
        }
        if($questionid && $questionuserid)
        return true;
        else
            return false;
    }
    public function getUserQuestions($user)
    {
         $query=$this->db->query("SELECT `expert_question`.`id`,`expert_question`.`question`,`expert_question`.`fromuser` FROM `expert_question` LEFT OUTER JOIN `expert_questionuser` ON `expert_questionuser`.`question`=`expert_question`.`id` WHERE `expert_questionuser`.`touser`='$user'")->result();
        foreach($query as $row)
        {
            $fromuser=$row->fromuser;
            $row->category=$this->db->query("SELECT `user`.`id`, `user`.`name`, `user`.`firstname`, `user`.`lastname`,`expert_profession`.`category` as `professioncategory`,`expert_hobby`.`category` as `hobbycategory` FROM `user` LEFT OUTER JOIN `expert_profession` ON `expert_profession`.`user`=`user`.`id` LEFT OUTER JOIN `expert_hobby` ON `expert_hobby`.`user`=`user`.`id` WHERE `user`.`id`='$fromuser'")->row();
            
        }
        return $query;
    }
    public function getUserReplies($user)
    {
        $query=$this->db->query("SELECT `expert_question`.`id`,`expert_question`.`question`,`expert_question`.`fromuser` FROM `expert_question` WHERE `expert_question`.`fromuser`='$user'")->row();
        $questionid=$query->question;
    
        $query1=$this->db->query("SELECT `user`.`id`,`user`.`firstname`,`user`.`lastname`,`user`.`image`,`expert_profession`.`category` as `profcat`,`expert_hobby`.`category` as `hobbycat` FROM `expert_questionuser` LEFT OUTER JOIN `user` ON `user`.`id`=`expert_questionuser`.`touser` LEFT OUTER JOIN `expert_profession` ON `expert_profession`.`user`=`expert_questionuser`.`touser` LEFT OUTER JOIN `expert_hobby` ON `expert_hobby`.`user`=`expert_questionuser`.`touser` WHERE `expert_questionuser`.`touser`='$user' AND `expert_questionuser`.`question`='$questionid'")->result();
        
        return $query1;
    }
    

}
?>
