<?php
declare(strict_types=1);

namespace App\EmailManager;

use App\Storage\QuoteSessionStorageInterface;
use Asdoria\SyliusQuoteRequestPlugin\Traits\QuoteSessionStorageTrait;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManagerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class MailingResourceSendListener
 * @package App\EmailManager
 *
 * @author  Hugo Duval <hugo.duval@asdoria.com>
 */
class ContactEmailManager implements ContactEmailManagerInterface
{
    use QuoteSessionStorageTrait;
    public function __construct(
        protected ContactEmailManagerInterface $inner
    )
    {
    }

    /**
     * @param array                 $data
     * @param array                 $recipients
     * @param ChannelInterface|null $channel
     * @param string|null           $localeCode
     *
     * @return void
     */
    public function sendContactRequest(
        array             $data,
        array             $recipients,
        ?ChannelInterface $channel = null,
        ?string           $localeCode = null,
    ): void
    {
        $this->inner->sendContactRequest($data);
        $this->quoteSessionStorage->removeForChannel($channel);
    
    }
 
}
