<?php

/**
 * job actions.
 *
 * @package    jobeet
 * @subpackage job
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class jobActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
#    $this->jobeetjobs = Doctrine_Core::getTable('jobeetjob')->getActiveJobs();
    $this->categories = Doctrine_Core::getTable('JobeetCategory')->getWithJobs();
 }

  public function executeShow(sfWebRequest $request)
  {
#    $this->job = Doctrine_Core::getTable('jobeetjob')->find(array($request->getParameter('id')));
    $this->job = $this->getRoute()->getObject();

    $this->getUser()->addJobToHistory($this->job);

}

  public function executeNew(sfWebRequest $request)
  {
  $job = new JobeetJob();
  $job->setType('part-time');
   $this->form = new JobeetJobForm($job);
 	
	}

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $this->form = new jobeetjobForm();
    $this->processForm($request, $this->form);
    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
#    $this->forward404Unless($jobeetjob = Doctrine_Core::getTable('jobeetjob')->find(array($request->getParameter('id'))), sprintf('Object jobeetjob does not exist (%s).', $request->getParameter('id')));
  $job = $this->getRoute()->getObject();
  $this->forward404If($job->getIsActivated());
 
  $this->form = new JobeetJobForm($job);

  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($jobeetjob = Doctrine_Core::getTable('jobeetjob')->find(array($request->getParameter('id'))), sprintf('Object jobeetjob does not exist (%s).', $request->getParameter('id')));
    $this->form = new jobeetjobForm($jobeetjob);
    $this->processForm($request, $this->form);
    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($jobeetjob = Doctrine_Core::getTable('jobeetjob')->find(array($request->getParameter('id'))), sprintf('Object jobeetjob does not exist (%s).', $request->getParameter('id')));
    $jobeetjob->delete();
    $this->redirect('job/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind(
		$request->getParameter($form->getName()),
		$request->getFiles($form->getName())
	);
    if ($form->isValid())
    {
		#save está dentro do modelo JobeetJob.class.php
		$jobeetjob = $form->save();
      $this->redirect('job/edit?token='.$jobeetjob->getToken());
    }
  }
  
  public function executePublish(sfWebRequest $request)
{
  $request->checkCSRFProtection();
 
  $job = $this->getRoute()->getObject();
  $job->publish();
 
  $this->getUser()->setFlash('notice', sprintf('Your job is now online for %s days.', sfConfig::get('app_active_days')));
 
  $this->redirect('job_show_user', $job);
}
  
}
