<?php
use Lexty\FlashyBundle\Util\Flashy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @author Alexandr Medvedev <medvedevav@niissu.ru>
 */

class FlashyTest extends KernelTestCase
{
    private static $container;
    /**
     * @var Flashy
     */
    private static $flashy;

    private static $storageKey = '_storage_key';
    private static $type = Flashy::TYPE_INFO;
    private static $delay = 3000;

    public static function setUpBeforeClass()
    {
        //start the symfony kernel
        $kernel = static::createKernel();
        $kernel->boot();

        //get the DI container
        self::$container = $kernel->getContainer();
    }

    /**
     * @dataProvider addTypeMessageProvider
     */
    public function testAddTypeMessage($typeValue, $typeMethod, $message, $delay = null)
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

    public function addTypeMessageProvider()
    {
        return [
            ['info', 'info', 'info message'],
            ['info', 'info', 'info message', 4000],
            ['success', 'success', 'success message'],
            ['success', 'success', 'success message', 4000],
            ['warning', 'warning', 'warning message'],
            ['warning', 'warning', 'warning message', 4000],
            ['error', 'error', 'error message'],
            ['error', 'error', 'error message', 4000],
            ['muted', 'muted', 'muted message'],
            ['muted', 'muted', 'muted message', 4000],
            ['muted-dark', 'mutedDark', 'mutedDark message'],
            ['muted-dark', 'mutedDark', 'mutedDark message', 4000],
            ['primary', 'primary', 'primary message'],
            ['primary', 'primary', 'primary message', 4000],
            ['primary-dark', 'primaryDark', 'primaryDark message'],
            ['primary-dark', 'primaryDark', 'primaryDark message', 4000],
        ];
    }

    public function _testAddMessage()
    {
        /** @var Session $session */
        $session = self::$container->get('session');
        $flashy = new Flashy($session, '_lexty_flashy', Flashy::TYPE_INFO, 2800);
        $flashy->addSuccess('Test success message!');
        $flashy->addSuccess('Test success message with custom delay!', 500);

        $flashy->add('Test message!');
        $flashy->add('Test error message!', Flashy::TYPE_ERROR);
        $flashy->add('Test error message with custom delay!', Flashy::TYPE_ERROR, 7000);

        $flashy->add(['message' => 'Test message from array']);
        $flashy->add(['message' => 'Test muted message from array', 'type' => Flashy::TYPE_MUTED]);
        $flashy->add(['message' => 'Test muted message with custom delay from array', 'type' => Flashy::TYPE_MUTED, 'delay' => 9000]);

        var_dump($session->getFlashBag()->all());
    }

}