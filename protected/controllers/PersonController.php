<?php
class PersonController extends Controller
{
    /**
     *
     * @var string the default layout for the views. Defaults to
     *      '//layouts/column2', meaning
     *      using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     *
     * @return array action filters
     */
    public function filters()
    {
        return array (
                'accessControl', // perform access control for CRUD operations
                'postOnly + delete'  // we only allow deletion via POST request
        );
    }

    public function actions()
    {
        return array (
                // captcha action renders the CAPTCHA image displayed on the
                // contact page
                'tree' => array (
                        'class' => 'TreeAction'
                ),
                'd3chart' => array (
                        'class' => 'D3Action'
                ),
                'circlechart' => array (
                        'class' => 'CircleChartAction'
                )
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     *
     * @return array access control rules
     */
    public function accessRules()
    {
        return array (
                array (
                        'allow', // allow all users to perform 'index' and
                                 // 'view' actions
                        'actions' => array (
                                'index',
                                'view',
                                'tree',
                                'circlechart',
                                'd3chart',
                                'distance'
                        ),
                        'users' => array (
                                '*'
                        )
                ),
                array (
                        'allow', // allow authenticated user to perform
                                 // 'create' and 'update' actions
                        'actions' => array (
                                'create',
                                'update'
                        ),
                        'users' => array (
                                '@'
                        )
                ),
                array (
                        'allow', // allow admin user to perform 'admin'
                                 // and 'delete' actions
                        'actions' => array (
                                'admin',
                                'delete'
                        ),
                        'users' => array (
                                'admin'
                        )
                ),
                array (
                        'deny', // deny all users
                        'users' => array (
                                '*'
                        )
                )
        );
    }

    /**
     * Displays a particular model.
     *
     * @param integer $id
     *            the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $model = $this->loadModel ( $id );
        $this->pageTitle = $model->name;
        RecentPerson::add ( $id );
        $this->render ( 'view', array (
                'model' => $model
        ) );
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view'
     * page.
     */
    public function actionCreate($spouse_id = 0, $mother_id = 0, $father_id = 0, $child_id = 0,$gender=-1)
    {
        $model = new Person ();
        $spouse = null;
        if ($spouse_id)
            $spouse = Person::model ()->findByPk ( $spouse_id );

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset ( $_POST ['Person'] ))
        {
            $model->attributes = $_POST ['Person'];
            if ($model->save ())
            {
                RecentPerson::add ( $model->cid );
                if ($spouse_id)
                {
                    $m = new Marriage ();
                    $m->husband_cid = $model->gender ? $model->cid : $spouse_id;
                    $m->wife_cid = $model->gender ? $spouse_id : $model->cid;
                    if ($m->save ())
                    {
                        //add spouse woman is called from existing id
                        $ltype = $model->gender ? Log::LOG_ADDSPOUSEWOMAN : Log::LOG_ADDSPOUSEMAN;
                        Log::l($ltype,$spouse_id,['spouse_id' => $model->cid]);

                        $this->redirect (
                                array (
                                        'view',
                                        'id' => $spouse_id
                                ) );
                    }
                    else
                    {
                        print_r ( $m->getErrors () );
                        die ();
                    }
                }
                else if ($mother_id)
                    $this->redirect (
                            array (
                                    'view',
                                    'id' => $mother_id
                            ) );
                else if ($child_id)
                {
                    $child = Person::model ()->findByPk ( $child_id );
                    if ($model->gender)
                    {
                        $ltype = Log::LOG_SETFATHER;
                        $child->father_cid = $model->cid;
                    }
                    else
                    {
                        $ltype = Log::LOG_SETMOTHER;
                        $child->mother_cid = $model->cid;
                    }
                    if (! $child->save ())
                    {
                        error_log ( print_r ( $child->errors, true ) );
                        throw new Exception ( "Saving child failed" );
                    }
                    Log::l($ltype,$child_id,['cid' => $model->cid]);

                    $this->redirect (
                            array (
                                    'view',
                                    'id' => $child_id
                            ) );
                }
                else
                    $this->redirect (
                            array (
                                    'view',
                                    'id' => $model->cid
                            ) );
            }
        }
        else if ($spouse)
        {
            $model->gender = intval ( ! $spouse->gender );
        }
        else if($child_id && $gender != -1)
        {
            $model->gender = $gender;
        }
        else
        {
            if ($mother_id)
                $model->mother_cid = $mother_id;
            if ($father_id)
                $model->father_cid = $father_id;
        }
        $this->render ( 'create',
                array (
                        'model' => $model,
                        'spouse' => $spouse
                ) );
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view'
     * page.
     *
     * @param integer $id
     *            the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel ( $id );

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $this->pageTitle = __ ( 'Update {name}', [
                '{name}' => $model->name
        ] );

        if (isset ( $_POST ['Person'] ))
        {
            $model->attributes = $_POST ['Person'];

            if ($model->save ())
                $this->redirect (
                        array (
                                'view',
                                'id' => $model->cid
                        ) );
        }

        $this->render ( 'update', array (
                'model' => $model
        ) );
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin'
     * page.
     *
     * @param integer $id
     *            the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $this->loadModel ( $id )->delete ();

        // if AJAX request (triggered by deletion via admin grid view), we
        // should not redirect the browser
        if (! isset ( $_GET ['ajax'] ))
            $this->redirect (
                    isset ( $_POST ['returnUrl'] ) ? $_POST ['returnUrl'] : array (
                            'admin'
                    ) );
    }

    /**
     * Lists all models.
     */
    public function actionIndex($gid = 0,$mru=0)
    {
        $p = new Person ();

        // looking at other's data is not blocked, yet
        if ($gid)
            $p->owner_gid = $gid;

        $crit = [];
        if($mru)
        {
            $crit['with'] = ['mru'];
            $crit['order'] = 'mru.dated desc';
            $crit['together'] = true;
        }

        $dataProvider = $p->search ($crit);
        $this->render ( 'index', array (
                'dataProvider' => $dataProvider
        ) );
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new Person ( 'search' );
        $model->unsetAttributes (); // clear any default values
        if (isset ( $_GET ['Person'] ))
            $model->attributes = $_GET ['Person'];

        $this->render ( 'admin', array (
                'model' => $model
        ) );
    }

    /**
     * Returns the data model based on the primary key given in the GET
     * variable.
     * If the data model is not found, an HTTP exception will be raised.
     *
     * @param integer $id
     *            the ID of the model to be loaded
     * @return Person the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = Person::model ()->findByPk ( $id );
        if ($model === null)
            throw new CHttpException ( 404, 'The requested page does not exist.' );
        return $model;
    }

    /**
     * Performs the AJAX validation.
     *
     * @param Person $model
     *            the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset ( $_POST ['ajax'] ) && $_POST ['ajax'] === 'person-form')
        {
            echo CActiveForm::validate ( $model );
            Yii::app ()->end ();
        }
    }

    /**
	 * To show statistics about the whole database
	 */
	public function actionDistance($max_level = 5,$limit = 200,$root_id=1)
	{
	    $this->render('distance',['max_level' => $max_level,'limit' => $limit,'root_id' => $root_id,'model' => Person::model()->findByPk($root_id)]);
	}
}
