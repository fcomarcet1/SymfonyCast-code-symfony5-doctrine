<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use App\Service\MarkdownHelper;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Sentry\State\HubInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Environment;

class QuestionController extends AbstractController
{
    private LoggerInterface    $logger;
    private bool               $isDebug;
    private QuestionRepository $questionRepository;

    public function __construct(
        QuestionRepository $questionRepository,
        LoggerInterface $logger,
        bool $isDebug)
    {
        $this->questionRepository = $questionRepository;
        $this->logger = $logger;
        $this->isDebug = $isDebug;
    }


    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(Environment $twigEnvironment): Response {
        /*
        // fun example of using the Twig service directly!
        $html = $twigEnvironment->render('question/homepage.html.twig');
        return new Response($html);
        */

        return $this->render('question/homepage.html.twig');
    }

    /**
     * @throws \Exception
     */
    #[Route('/questions/new', name: 'app_question_new')]
    public function new(): Response
    {
        $question = new Question();
        $question->setName('Missing pants')
            ->setSlug('missing-pants-'.rand(100, 999))
            ->setQuestion(
                <<<EOF
                    Hi! So... I'm having a *weird* day. Yesterday, I cast a spell
                    to make my dishes wash themselves. But while I was casting it,
                    I slipped a little and I think `I also hit my pants with the spell`.
                    When I woke up this morning, I caught a quick glimpse of my pants
                    opening the front door and walking out! I've been out all afternoon
                    (with no pants mind you) searching for them.
                    Does anyone have a spell to call your pants back?
                EOF
            );

        if (rand(1, 10) > 2) {
            $question->setAskedAt(new \DateTime(sprintf('-%d days', rand(1, 100))));
        }

        // save contains persist and flush
        $this->questionRepository->save($question, true);

        return new Response(sprintf(
            'Well hallo! The shiny new question is id #%d, slug: %s',
            $question->getId(),
            $question->getSlug()
        ));
    }

    /**
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    #[Route('/questions/{slug}', name: 'app_question_show')]
    public function show(
        string $slug,
        QuestionRepository $questionRepository,
        MarkdownHelper $markdownHelper,
    ): Response {

        //dump($sentryHub->getClient());

        if ($this->isDebug) {
            $this->logger->info('We are in debug mode!');
        }

        $question = $questionRepository->findOneBy([
            'slug' => $slug
        ]);
        if (!$question) {
            throw $this->createNotFoundException(sprintf('no question found for slug "%s"', $slug));
        }

        $answers = [
            'Make sure your cat is sitting purrrfectly still 🤣',
            'Honestly, I like furry shoes better than MY cat',
            'Maybe... try saying the spell backwards?',
        ];

        $questionText = 'I\'ve been turned into a cat, any thoughts on how to turn back? While I\'m **adorable**, I don\'t really care for cat food.';
        //$parsedQuestionText = $markdownParser->transformMarkdown($questionText);

        // Reading a Parameter in a Controller
        //dump($this->getParameter('cache_adapter'));

        // call service markdownHelper
        $parsedQuestionText = $markdownHelper->parse($questionText);

        return $this->render('question/show.html.twig', [
            'question' => $question,
            'answers' => $answers,
        ]);

        /*return $this->render('question/show.html.twig', [
            'question'     => ucwords(str_replace('-', ' ', $slug)),
            'answers'      => $answers,
            'questionText' => $parsedQuestionText,
        ]);*/
    }


}
