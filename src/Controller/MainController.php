<?php
namespace App\Controller;

use App\Entity\Comment;
use App\Service\ParserMusic;
use App\Service\SaveTopMusic;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MainController extends AbstractController
{
    const WEEKENDS = [ '01-01', '05-30'];
    /**
     * @Route("/", name="index")
     * @Template()
     */
    public function indexAction()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/taskone", name="taskone")
     * @Template()
     */
    public function taskoneAction()
    {
        $values = [];

        for ($i = 0; $i < 100; $i++) {
            $values[]=rand(1,10);
        }

        $countValues = array_count_values($values);
        asort($countValues);

        $maxValue = array_search(max($countValues),$countValues);

        return $this->render(
            'task_one.html.twig',
            ['values' => $values, 'countValues' => $countValues, 'maxValue' => $maxValue,]
        );
    }

    /**
     * @Route("/tasktwo", name="tasktwo")
     */
    public function tasktwoAction()
    {
        $dateDelivery = $this->getDateDelivery();

        if ($this->checkWeekends($dateDelivery)){
            $dateDelivery->modify('+1 day');
        }

        return $this->render(
            'task_two.html.twig',
            [
                'dateDelivery' => $dateDelivery->format('d M'),
                'weekends' => self::WEEKENDS,
            ]
        );
    }

    /**
     * @Route("/taskthree", name="taskthree")
     */
    public function taskThreeAction(Request $request)
    {
        $comment = new Comment();

        $form = $this->createFormBuilder($comment)
            ->add('email', EmailType::class, ['attr' => ['data-min-length' => 6, 'data-max-length' => 20, 'data-validate-length' => '1', 'data-validate-email' => '1']])
            ->add('name', TextType::class, ['attr' => ['data-min-length' => 4, 'data-max-length' => 10, 'data-validate-length' => '1']])
            ->add('text', TextType::class, ['attr' => ['data-min-length' => 2, 'data-max-length' => 20, 'data-validate-length' => '1']])
            ->add('save', SubmitType::class, ['label' => 'Post comment'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Comment save!');

//            mail($comment->getEmail(), 'Comment save!', 'Comment save!');
        }

        return $this->render(
            'task_three.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/taskfour", name="taskfour")
     * @param ParserMusic $parserMusic
     * @param SaveTopMusic $saveTopMusic
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function taskFourAction(ParserMusic $parserMusic, SaveTopMusic $saveTopMusic)
    {
        $musicTop = $parserMusic->parseTopChart();

        $saveTopMusic->saveToFileMusic($musicTop);

        return $this->render(
            'task_four.html.twig',
            [
                'musicTop' => $musicTop, 'topBy' => $parserMusic::URL
            ]
        );
    }

    /**
     * Метод возвращает дату доставки в зависимости от текущего времени
     *
     * @return \DateTime
     * @throws \Exception
     */
    private function getDateDelivery()
    {
        $dateNow = new \DateTime('now');
        $dateEndWork = new \DateTime("21:00");
        $dateDelivery = new \DateTime('+1 day');

        if ($dateNow > $dateEndWork) {
            $dateDelivery->modify('+1 day');
        }

        return $dateDelivery;
    }

    /**
     * Метод проверяет выпадает ли дата доставки на праздники
     *
     * @param \DateTime $dateDelivery
     * @return false|int|string
     */
    private function checkWeekends($dateDelivery)
    {
        return array_search($dateDelivery->format('m-d'), self::WEEKENDS);
    }
}