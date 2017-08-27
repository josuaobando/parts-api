<?php

/**
 * @author Josua
 */
class CoreConfig
{

  /**
   * Cache version to JS and CSS
   */
  const CACHE_VERSION = 1.0;

  /**
   * define if the current environment is development or production
   *
   * @var bool
   */
  const DEV = true;

  /**
   * define if we must print the exception or not.
   * DEV must be in true
   *
   * @var bool
   */
  const PRINT_EXCEPTIONS = false;

  /**
   * Webservices global timeout
   */
  const WS_TIMEOUT = 10;

  /**
   * Web services global connection timeout
   */
  const WS_TIMEOUT_ON_CONNECT = 10;

  /**
   * max execution time
   */
  const MAX_EXECUTION_TIME = 90;

  /**
   * Encryptation key
   */
  const ENCRIPT_KEY = "123";

  /**
   * Notify warnings for each statement executed in database
   */
  const DB_NOTIFY_WARNINGS = false;

  /**
   * Database configuration
   */
  const DB_HOSTNAME__ = 'DB_HOSTNAME__';
  const DB_USERNAME__ = 'DB_USERNAME__';
  const DB_PASSWORD__ = 'DB_PASSWORD__';

  //development main database
  const DB_NAME = 'software_api';
  const DB_HOSTNAME__software_api = 'MzUuMTg0LjE2NS40Mg=='; //localhost
  const DB_USERNAME__software_api = 'am9iYW5kb2M=';     //jobandoc
  const DB_PASSWORD__software_api = 'ajI5MDlP';  //j2909O

  /**
   * Mail configuration
   */
  const MAIL_SEND_ACTIVE = false;
  const MAIL_STANDARD = false;
  const MAIL_RETURN = 'josua@midascashier.com';
  const MAIL_FROM = 'josua@midascashier.com';
  const MAIL_HOST = 'srv-mail1.im.priv';
  const MAIL_PORT = '25';
  const MAIL_USERNAME = 'josua@midascashier.com';
  const MAIL_PASSWORD = '123';
  const MAIL_AUTH = false;
  const MAIL_DEV = 'josua@midascashier.com';

  /**
   * URL where the message queue service is located
   */
  const MESSAGE_QUEUE_URL = 'http://api:8080/mqueue/ws/mQueueProcessor.php';

  /**
   * path where all system logs will be stored
   *
   * @var string
   */
  const LOG_PATH = "C:/Logs";

  /**
   * options to configure the db stats tracking process
   * these are the filters we can set for db tracking
   */
  const TRACK_DB_STATS_ACTIVE = true;

  /**
   * list of DB usernames to be tracked
   *
   * user1|user2|...|userX
   * wildcard for all users = *
   */
  const TRACK_DB_STATS_USERS = '*';

  /**
   * list of DB names to be tracked
   *
   * db1|db2|...|dbX
   * wildcard for all dbs = *
   */
  const TRACK_DB_STATS_DBS = '*';

  /**
   * list of DB server hosts to be tracked
   *
   * host1|host2|...|hostX
   * wildcard for all hosts = *
   */
  const TRACK_DB_STATS_HOSTS = '*';

  /**
   * filter to track DB executions taking more than the value set (seconds)
   *
   * @var float
   */
  const TRACK_DB_STATS_TIME = 0;

  /**
   * options to configure the webservices stats tracking process
   * these are the filters we can set for webservices tracking
   */
  const TRACK_WS_STATS_ACTIVE = true;

  /**
   * filter to track WS executions taking more than the value set (seconds)
   *
   * @var float
   */
  const TRACK_WS_STATS_TIME = 0;

  /**
   * urls must match this defined pattern
   *
   * last i is for a case-insensitive search
   *
   * example:
   * TRACK_WS_STATS_PATTERN = "/google|yahoo/i";
   *
   * set to null to match anything
   *
   * @var string
   */
  const TRACK_WS_STATS_PATTERN = null;

  /**
   * timezone for application (set to America/Costa_Rica)
   *
   * @var string
   */
  const TIMEZONE_APP = 'America/Costa_Rica';

  /**
   * Encoding currently used in the system. [UTF-8 | ISO-8859-1]
   */
  const SYS_ENCODING = 'UTF-8';

}

?>