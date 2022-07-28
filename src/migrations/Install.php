<?php
/**
 * Stripe Webhooks plugin for Craft CMS 3.x.
 *
 * Handle Stripe webhooks in a CraftCMS application
 *
 * @link      https://owl-design.net
 *
 * @copyright Copyright (c) 2022 Vadim Goncharov
 */

namespace owldesign\stripewebhooks\migrations;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * @author    Vadim Goncharov
 *
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public string $driver;

    // Public Methods
    // =========================================================================

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables(): bool
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%stripewebhooks_calls}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%stripewebhooks_calls}}',
                [
                    'id'          => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid'         => $this->uid(),
                    'siteId'      => $this->integer()->notNull(),
                    'type'        => $this->string(255),
                    'payload'     => $this->text(),
                    'exception'   => $this->text(),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function addForeignKeys(): void
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%stripewebhooks_calls}}', 'siteId'),
            '{{%stripewebhooks_calls}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @return void
     */
    protected function removeTables(): void
    {
        $this->dropTableIfExists('{{%stripewebhooks_calls}}');
    }
}
