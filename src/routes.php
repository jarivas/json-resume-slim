<?php

use App\Helper\App;
use App\Controller\Auth\Login;
use App\Controller\Auth\Logout;
use App\Controller\Auth\RefreshToken;
use App\Controller\Award\Create as CreateAward;
use App\Controller\Award\Delete as DeleteAward;
use App\Controller\Award\Get as GetAward;
use App\Controller\Award\GetById as GetAwardById;
use App\Controller\Award\Update as UpdateAward;
use App\Controller\Basic\Create as CreateBasic;
use App\Controller\Basic\Delete as DeleteBasic;
use App\Controller\Basic\Get as GetBasic;
use App\Controller\Basic\GetById as GetBasicById;
use App\Controller\Basic\Update as UpdateBasic;
use App\Controller\Certificate\Create as CreateCertificate;
use App\Controller\Certificate\Delete as DeleteCertificate;
use App\Controller\Certificate\Get as GetCertificate;
use App\Controller\Certificate\GetById as GetCertificateById;
use App\Controller\Certificate\Update as UpdateCertificate;
use App\Controller\Education\Create as CreateEducation;
use App\Controller\Education\Delete as DeleteEducation;
use App\Controller\Education\Get as GetEducation;
use App\Controller\Education\GetById as GetEducationById;
use App\Controller\Education\Update as UpdateEducation;
use App\Controller\Interest\Create as CreateInterest;
use App\Controller\Interest\Delete as DeleteInterest;
use App\Controller\Interest\Get as GetInterest;
use App\Controller\Interest\GetById as GetInterestById;
use App\Controller\Interest\Update as UpdateInterest;
use App\Controller\Language\Create as CreateLanguage;
use App\Controller\Language\Delete as DeleteLanguage;
use App\Controller\Language\Get as GetLanguage;
use App\Controller\Language\GetById as GetLanguageById;
use App\Controller\Language\Update as UpdateLanguage;
use App\Controller\Project\Create as CreateProject;
use App\Controller\Project\Delete as DeleteProject;
use App\Controller\Project\Get as GetProject;
use App\Controller\Project\GetById as GetProjectById;
use App\Controller\Project\Update as UpdateProject;
use App\Controller\Publication\Create as CreatePublication;
use App\Controller\Publication\Delete as DeletePublication;
use App\Controller\Publication\Get as GetPublication;
use App\Controller\Publication\GetById as GetPublicationById;
use App\Controller\Publication\Update as UpdatePublication;
use App\Controller\Reference\Create as CreateReference;
use App\Controller\Reference\Delete as DeleteReference;
use App\Controller\Reference\Get as GetReference;
use App\Controller\Reference\GetById as GetReferenceById;
use App\Controller\Reference\Update as UpdateReference;
use App\Controller\Skill\Create as CreateSkill;
use App\Controller\Skill\Delete as DeleteSkill;
use App\Controller\Skill\Get as GetSkill;
use App\Controller\Skill\GetById as GetSkillById;
use App\Controller\Skill\Update as UpdateSkill;
use App\Controller\Volunteer\Create as CreateVolunteer;
use App\Controller\Volunteer\Delete as DeleteVolunteer;
use App\Controller\Volunteer\Get as GetVolunteer;
use App\Controller\Volunteer\GetById as GetVolunteerById;
use App\Controller\Volunteer\Update as UpdateVolunteer;
use App\Controller\Work\Create as CreateWork;
use App\Controller\Work\Delete as DeleteWork;
use App\Controller\Work\Get as GetWork;
use App\Controller\Work\GetById as GetWorkById;
use App\Controller\Work\Update as UpdateWork;
use App\Middleware\Route\Authentication;

$app = App::getApp();

$app->post('/auth/login', Login::class);

$app->group(
    '/auth',
    function ($group) {
    $group->post('/logout', Logout::class);
    $group->post('/refresh-token', RefreshToken::class);
    }
)->add(Authentication::class);

$app->group(
    '/basic',
    function ($group) {
    $group->post('', CreateBasic::class);
    $group->get('', GetBasic::class);
    $group->get('/{basic_id}', GetBasicById::class);
    $group->patch('/{basic_id}', UpdateBasic::class);
    $group->delete('/{basic_id}', DeleteBasic::class);
    }
)->add(Authentication::class);

$app->group(
    '/award',
    function ($group) {
    $group->post('', CreateAward::class);
    $group->get('', GetAward::class);
    $group->get('/{award_id}', GetAwardById::class);
    $group->patch('/{award_id}', UpdateAward::class);
    $group->delete('/{award_id}', DeleteAward::class);
    }
)->add(Authentication::class);

$app->group(
    '/certificate',
    function ($group) {
    $group->post('', CreateCertificate::class);
    $group->get('', GetCertificate::class);
    $group->get('/{certificate_id}', GetCertificateById::class);
    $group->patch('/{certificate_id}', UpdateCertificate::class);
    $group->delete('/{certificate_id}', DeleteCertificate::class);
    }
)->add(Authentication::class);

$app->group(
    '/education',
    function ($group) {
    $group->post('', CreateEducation::class);
    $group->get('', GetEducation::class);
    $group->get('/{education_id}', GetEducationById::class);
    $group->patch('/{education_id}', UpdateEducation::class);
    $group->delete('/{education_id}', DeleteEducation::class);
    }
)->add(Authentication::class);

$app->group(
    '/interest',
    function ($group) {
    $group->post('', CreateInterest::class);
    $group->get('', GetInterest::class);
    $group->get('/{interest_id}', GetInterestById::class);
    $group->patch('/{interest_id}', UpdateInterest::class);
    $group->delete('/{interest_id}', DeleteInterest::class);
    }
)->add(Authentication::class);

$app->group(
    '/language',
    function ($group) {
    $group->post('', CreateLanguage::class);
    $group->get('', GetLanguage::class);
    $group->get('/{language_id}', GetLanguageById::class);
    $group->patch('/{language_id}', UpdateLanguage::class);
    $group->delete('/{language_id}', DeleteLanguage::class);
    }
)->add(Authentication::class);

$app->group(
    '/project',
    function ($group) {
    $group->post('', CreateProject::class);
    $group->get('', GetProject::class);
    $group->get('/{project_id}', GetProjectById::class);
    $group->patch('/{project_id}', UpdateProject::class);
    $group->delete('/{project_id}', DeleteProject::class);
    }
)->add(Authentication::class);

$app->group(
    '/publication',
    function ($group) {
    $group->post('', CreatePublication::class);
    $group->get('', GetPublication::class);
    $group->get('/{publication_id}', GetPublicationById::class);
    $group->patch('/{publication_id}', UpdatePublication::class);
    $group->delete('/{publication_id}', DeletePublication::class);
    }
)->add(Authentication::class);

$app->group(
    '/reference',
    function ($group) {
    $group->post('', CreateReference::class);
    $group->get('', GetReference::class);
    $group->get('/{reference_id}', GetReferenceById::class);
    $group->patch('/{reference_id}', UpdateReference::class);
    $group->delete('/{reference_id}', DeleteReference::class);
    }
)->add(Authentication::class);

$app->group(
    '/skill',
    function ($group) {
    $group->post('', CreateSkill::class);
    $group->get('', GetSkill::class);
    $group->get('/{skill_id}', GetSkillById::class);
    $group->patch('/{skill_id}', UpdateSkill::class);
    $group->delete('/{skill_id}', DeleteSkill::class);
    }
)->add(Authentication::class);

$app->group(
    '/volunteer',
    function ($group) {
    $group->post('', CreateVolunteer::class);
    $group->get('', GetVolunteer::class);
    $group->get('/{volunteer_id}', GetVolunteerById::class);
    $group->patch('/{volunteer_id}', UpdateVolunteer::class);
    $group->delete('/{volunteer_id}', DeleteVolunteer::class);
    }
)->add(Authentication::class);

$app->group(
    '/work',
    function ($group) {
    $group->post('', CreateWork::class);
    $group->get('', GetWork::class);
    $group->get('/{work_id}', GetWorkById::class);
    $group->patch('/{work_id}', UpdateWork::class);
    $group->delete('/{work_id}', DeleteWork::class);
    }
)->add(Authentication::class);
