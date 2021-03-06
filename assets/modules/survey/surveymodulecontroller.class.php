<?php

class SurveyModuleController
{
    /**
     * @var Surveys
     */
    protected $app;
    /**
     * @var SurveyResponse
     */
    protected $response;

    /**
     * SurveyModuleController constructor.
     *
     * @param Surveys $app
     */
    public function __construct(Surveys $app)
    {
        $this->app = $app;
        $this->response = $app->ajaxResponse();

        $app->view()->share('id', isset($_GET['id']) ? (int) $_GET['id'] : 0);
    }

    /**
     * @return string|View
     */
    public function indexAction()
    {
        if ($this->app->isInstalled()) {
            $tpl = 'module.index';
            $title = $this->app->t('surveys');
        } else {
            $tpl = 'module.install';
            $title = $this->app->t('installing');
        }

        return $this->app->view('module.layout', array(
            'content' => $this->app->view($tpl,
                $this->app->isInstalled() ? array(
                    'surveys' => $this->app->getAllSurveys(),
                ) : null),
            'title' => $title,
        ));
    }

    /**
     * @return string|SurveyResponse|View
     */
    public function createAction()
    {
        if (!isMethod('post')) {
            return $this->app->view('module.layout', array(
                'content' => $this->app->view('module.update', array(
                    'survey' => $this->app->survey(),
                )),
                'title' => $this->app->t('survey_creating'),
            ));
        };

        $create = $this->app->createSurvey($_POST);

        if (is_array($create)) {
            $this->response->setMessage($this->app->t('submit_error'))->setErrors($create);
        } else {
            $this->response->setMessage($this->app->t('success_created'));
        }

        return $this->response;
    }

    /**
     * @return SurveyResponse
     */
    protected function save()
    {
        $save = $this->app->updateSurvey($_POST);

        if (is_array($save)) {
            $this->response->setErrors($save)->setMessage($this->app->t('submit_error'));
        } else {
            $this->response->isError(!$save)->setMessage($this->app->t(
                !$save ? 'not_exist' : 'success_updated'
            ));
        }

        return $this->response;
    }

    /**
     * @return null|string|SurveyResponse|View
     */
    public function updateAction()
    {
        if (isMethod('post')) {
            return $this->save();
        }

        if (!isset($_GET['survey']) || !($id = (int) $_GET['survey'])) {
            return null;
        }

        return $this->app->view('module.layout', array(
            'content' => $this->app->view('module.update', array(
                'survey' => $this->app->getSurveyWithOptions($id),
            )),
            'title' => $this->app->t('survey_editing'),
        ));
    }

    /**
     * Delete survey
     *
     * @return SurveyResponse
     */
    public function deleteAction()
    {
        if (!isset($_GET['survey']) || !($id = (int) $_GET['survey'])) {
            return $this->response->setMessage($this->app->t('survey_id_error'))->isError();
        }

        $delete = $this->app->deleteSurvey($id);

        return $this->response
            ->isError(!$delete)
            ->setMessage($this->app->t(!$delete ? 'not_exist' : 'success_deleted'));
    }

    /**
     * @return SurveyResponse
     */
    public function closeAction()
    {
        if (!isset($_GET['survey']) || !($id = (int) $_GET['survey'])) {
            return $this->response->isError()->setMessage($this->app->t('survey_id_error'));
        }

        $survey = $this->app->closeSurvey($id);

        if ($survey === false) {
            return $this->response->isError()->setMessage($this->app->t('not_exist'));
        }

        return $this->response->setMessage($this->app->t(
            $survey->isClosed() ? 'success_opened' : 'success_closed')
        );
    }

    /**
     * @return SurveyResponse
     */
    public function resetAction()
    {
        if (!isset($_GET['survey']) || !($id = (int) $_GET['survey'])) {
            return $this->response->isError()->setMessage($this->app->t('survey_id_error'));
        }

        $reset = $this->app->resetSurvey($id);

        return $this->response->isError(!$reset)->setMessage($this->app->t(
            $reset ? 'success_reset' : 'not_exist'
        ));
    }

    /**
     * @return string
     */
    public function installAction()
    {
        $install = $this->app->install();

        return $this->app->view('module.layout', array(
            'content' => $this->app->view('module.install', array(
                'message' => $install,
            )),
            'title' => $this->app->t('installing'),
        ));
    }

    /**
     * @return null|string
     */
    public function infoAction()
    {
        if (!isset($_GET['survey']) || !($id = (int) $_GET['survey'])) {
            return null;
        }

        return $this->app->view('module.layout', array(
            'content' => $this->app->view('module.info', array(
                'survey' => $this->app->getSurveyWithRelations($id),
            )),
            'title' => $this->app->t('survey_info'),
        ));
    }

    /**
     * @param string $action
     *
     * @return null
     */
    public function run($action)
    {
        $action = $action . 'Action';
        if (!method_exists($this, $action)) {
            return null;
        }

        $output = $this->{$action}();

        if ($output === null) {
            return null;
        }

        if (is_array($output)) {
            $response = $this->app->ajaxResponse();
            $output = $response->setData($output);
        }

        if ($output instanceof SurveyResponse) {
            header('Content-Type: application/json; charset=UTF-8');
            echo $output->toJson();
            die;
        }

        return $output;
    }
}
