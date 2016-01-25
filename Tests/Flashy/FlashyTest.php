<?php
use Lexty\FlashyBundle\Flashy\Flashy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Session\Session;

class FlashyTest extends KernelTestCase
{
    private static $container;

    private static $storageKey = '_storage_key';
    private static $type = Flashy::TYPE_INFO;
    private static $delay = 3000;

    public static function setUpBeforeClass()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        self::$container = $kernel->getContainer();
    }

    /**
     * @dataProvider addMessageProvider
     */
    public function testAddTypeMessage($message, $typeValue = null, $typeMethod = null, $delay = null)
    {
        /** @var Session $session */
        $session = self::$container->get('session');
        $flashy = new Flashy($session, self::$storageKey, self::$type, self::$delay);
        call_user_func([$flashy, "add$typeMethod"], $message, $delay);
        $this->assertEquals(
            [self::$storageKey => [['message' => "$typeMethod message", 'type' => $typeValue, 'delay' => $delay ?: self::$delay]]],
            $session->getFlashBag()->all()
        );
    }

    /**
     * @dataProvider addMessageWithEmptyTypesProvider
     */
    public function addMessageProvider()
    {
        return [
            ['info message', 'info', 'info', ],
            ['info message', 'info', 'info', 4000],
            ['success message', 'success', 'success', ],
            ['success message', 'success', 'success', 4000],
            ['warning message', 'warning', 'warning', ],
            ['warning message', 'warning', 'warning', 4000],
            ['error message', 'error', 'error', ],
            ['error message', 'error', 'error', 4000],
            ['muted message', 'muted', 'muted', ],
            ['muted message', 'muted', 'muted', 4000],
            ['mutedDark message', 'muted-dark', 'mutedDark', ],
            ['mutedDark message', 'muted-dark', 'mutedDark', 4000],
            ['primary message', 'primary', 'primary', ],
            ['primary message', 'primary', 'primary', 4000],
            ['primaryDark message', 'primary-dark', 'primaryDark', ],
            ['primaryDark message', 'primary-dark', 'primaryDark', 4000],
        ];
    }

    /**
     * @dataProvider addMessageWithDefaultAndCustomTypesProvider
     */
    public function testAddArgsMessage($message, $typeValue = null, $typeMethod = null, $delay = null)
    {
        /** @var Session $session */
        $session = self::$container->get('session');
        $flashy = new Flashy($session, self::$storageKey, self::$type, self::$delay);
        $flashy->add($message, $typeValue, $delay);
        $this->assertEquals(
            [self::$storageKey => [[
                'message' => ($typeMethod ?: self::$type) . ' message',
                'type'    => $typeValue ?: self::$type,
                'delay'   => $delay ?: self::$delay
            ]]],
            $session->getFlashBag()->all()
        );
    }

    /**
     * @dataProvider addMessageWithDefaultAndCustomTypesProvider
     */
    public function testAddArrayMessage($message, $typeValue = null, $typeMethod = null, $delay = null)
    {
        /** @var Session $session */
        $session = self::$container->get('session');
        $flashy = new Flashy($session, self::$storageKey, self::$type, self::$delay);
        $params = ['message' => $message];
        if ($typeValue) {
            $params['type'] = $typeValue;
        }
        if ($delay) {
            $params['delay'] = $delay;
        }
        $flashy->add($params);
        $this->assertEquals(
            [self::$storageKey => [[
                'message' => ($typeMethod ?: self::$type) . ' message',
                'type'    => $typeValue ?: self::$type,
                'delay'   => $delay ?: self::$delay
            ]]],
            $session->getFlashBag()->all()
        );
    }

    public function addMessageWithDefaultAndCustomTypesProvider()
    {
        return array_merge($this->addMessageProvider(),  [
            [self::$type . ' message', null, null, 4000],
            [self::$type . ' message'],
            ['custom type message', 'customtype', 'custom type', 4000],
            ['custom type message', 'customtype', 'custom type'],
        ]);
    }

}