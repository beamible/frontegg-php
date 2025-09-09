<?php

namespace Frontegg\Events\Config;

use Frontegg\Events\Channel\AuditPropertiesInterface;
use Frontegg\Events\Channel\BellPropertiesInterface;
use Frontegg\Events\Channel\SlackChatPostMessageArgumentsInterface;
use Frontegg\Events\Channel\WebHookBody;
use Frontegg\Events\Channel\WebPushPropertiesInterface;

interface ChannelsConfigInterface extends SerializableInterface
{
    /**
     * @return WebHookBody|UseChannelDefaults|null
     */
    public function getWebHook(): WebHookBody|UseChannelDefaults|null;

    /**
     * @return SlackChatPostMessageArgumentsInterface|UseChannelDefaults|null
     */
    public function getSlack(): SlackChatPostMessageArgumentsInterface|UseChannelDefaults|null;

    /**
     * @return WebPushPropertiesInterface|UseChannelDefaults|null
     */
    public function getWebPush(): WebPushPropertiesInterface|UseChannelDefaults|null;

    /**
     * @return AuditPropertiesInterface|UseChannelDefaults|null
     */
    public function getAudit(): AuditPropertiesInterface|UseChannelDefaults|null;

    /**
     * @return BellPropertiesInterface|UseChannelDefaults|null
     */
    public function getBell(): BellPropertiesInterface|UseChannelDefaults|null;

    /**
     * Check if at least one channel is configured.
     *
     * @return bool
     */
    public function isConfigured(): bool;
}
