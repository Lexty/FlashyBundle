<?php

namespace Lexty\FlashyBundle;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Flashy message container.
 *
 * Works over `FlashBag`.
 *
 * @author Alexandr Medvedev <alexnadr.mdr@gmail.com>
 */
class Flashy
{
    const TYPE_INFO         = 'info';
    const TYPE_SUCCESS      = 'success';
    const TYPE_WARNING      = 'warning';
    const TYPE_ERROR        = 'error';
    const TYPE_MUTED        = 'muted';
    const TYPE_MUTED_DARK   = 'muted-dark';
    const TYPE_PRIMARY      = 'primary';
    const TYPE_PRIMARY_DARK = 'primary-dark';

    const KEY_MESSAGE = 'message';
    const KEY_TYPE    = 'type';
    const KEY_DELAY   = 'delay';

    /**
     * Storage.
     *
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * The storage key for flashes in the session.
     *
     * @var string
     */
    private $storageKey;

    /**
     * The default type of flashes.
     *
     * @var string
     */
    private $defaultType;

    /**
     * The default delay of flashes.
     *
     * @var int
     */
    private $defaultDelay;

    /**
     * Constructor.
     *
     * @param Session $session      The user session.
     * @param string  $storageKey   The key used to store flashes in the bag.
     * @param string  $defaultType  The default type of flashes.
     * @param int     $defaultDelay The default delay of flashes.
     */
    public function __construct(Session $session, $storageKey, $defaultType, $defaultDelay)
    {
        $this->flashBag = $session->getFlashBag();
        $this->storageKey = $storageKey;
        $this->defaultType = $defaultType;
        $this->defaultDelay = $defaultDelay;
    }

    /**
     * Adds a flash message.
     *
     * @param array|string $flash
     * @param string|null  $type
     * @param int|null     $delay
     */
    public function add($flash, $type = null, $delay = null)
    {
        $flash = $this->prepare($flash, $type, $delay);

        $this->flashBag->add($this->storageKey, $flash);
    }

    /**
     * Gets flashes.
     *
     * @return array
     */
    public function peek()
    {
        return $this->flashBag->peek($this->storageKey);
    }

    /**
     * Gets and clears flash from the stack.
     *
     * @return array
     */
    public function get()
    {
        return $this->flashBag->get($this->storageKey);
    }

    /**
     * Gets and clears flashes from the stack.
     *
     * @return array
     */
    public function all()
    {
        $result = $this->peek();
        $this->flashBag->set($this->storageKey, []);

        return $result;
    }

    /**
     * Registers a message.
     *
     * @param string|array $flashes
     * @param string|null  $type
     * @param int|null     $delay
     */
    public function set($flashes, $type = null, $delay = null)
    {
        if (!is_array($flashes) || isset($flashes[self::KEY_MESSAGE]) || isset($flashes[self::KEY_TYPE]) || isset($flashes[self::KEY_DELAY])) {
            $flashes = $this->prepare($flashes, $type, $delay = null);
        } else {
            array_walk($flashes, [$this, 'prepare'], [$type, $delay]);
        }

        $this->flashBag->set($this->storageKey, $flashes);
    }

    /**
     * Sets all flash messages.
     *
     * @param array $flashes
     */
    public function setAll(array $flashes)
    {
        $this->flashBag->set($this->storageKey, $flashes);
    }

    /**
     * Has flash messages?
     *
     * @return bool
     */
    public function has()
    {
        return $this->flashBag->has($this->storageKey);
    }

    /**
     * Clears out data from bag.
     */
    public function clear()
    {
        return $this->all();
    }

    /**
     * @param string|array $flash
     * @param string|null  $type
     * @param int|null     $delay
     *
     * @return array
     */
    private function prepare($flash, $type, $delay) {
        if (!is_array($flash)) {
            if (null === $type) {
                $type = $this->defaultType;
            }
            if (null === $delay) {
                $delay = $this->defaultDelay;
            }
            $flash = [
                self::KEY_MESSAGE => (string)$flash,
                self::KEY_TYPE    => (string)$type,
                self::KEY_DELAY   => (int)$delay,
            ];
        }

        if (!isset($flash[self::KEY_TYPE])) {
            $flash[self::KEY_TYPE] = $this->defaultType;
        }
        if (!isset($flash[self::KEY_DELAY])) {
            $flash[self::KEY_DELAY] = $this->defaultDelay;
        }

        return $flash;
    }
}
