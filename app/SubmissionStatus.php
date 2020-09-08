<?php

namespace App;

use MyCLabs\Enum\Enum;

/**
 * Class SubmissionStatus
 * @package App
 * @method static SubmissionStatus NONE()
 * @method static SubmissionStatus REMOVED()
 * @method static SubmissionStatus QUEUED()
 * @method static SubmissionStatus PARSING()
 * @method static SubmissionStatus PARSED()
 * @method static SubmissionStatus ERROR_FAILURE()
 * @method static SubmissionStatus ERROR_MISSING_NODE()
 * @method static SubmissionStatus ERROR_UNKNOWN_DROPS()
 * @method static SubmissionStatus ERROR_INVALID_DROPS()
 * @method static SubmissionStatus ERROR_MISSING_DROPS()
 * @method static SubmissionStatus ERROR_QP_MISMATCH()
 * @method static SubmissionStatus SUCCESS()
 * @method static SubmissionStatus SUCCESS_MULTIPLE()
 */
class SubmissionStatus extends Enum
{

    private const NONE = 0;
    private const REMOVED = 1;

    private const QUEUED = 10;
    private const PARSING = 11;
    private const PARSED = 12;

    private const ERROR_FAILURE = 20;
    private const ERROR_MISSING_NODE = 21;
    private const ERROR_UNKNOWN_DROPS = 22;
    private const ERROR_INVALID_DROPS = 23;
    private const ERROR_MISSING_DROPS = 24;
    private const ERROR_QP_MISMATCH = 25;

    private const SUCCESS = 30;
    private const SUCCESS_MULTIPLE = 31;

}
