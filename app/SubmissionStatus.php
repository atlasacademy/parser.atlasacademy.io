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
 * @method static SubmissionStatus SUCCESS_MATCHED()
 */
class SubmissionStatus extends Enum
{

    private const NONE = 0;
    private const REMOVED = 1;

    private const QUEUED = 10;
    private const PARSING = 11;
    private const PARSED = 12;

    private const ERROR_FAILURE = 100;
    private const ERROR_MISSING_NODE = 101;
    private const ERROR_UNKNOWN_DROPS = 102;
    private const ERROR_INVALID_DROPS = 103;
    private const ERROR_MISSING_DROPS = 104;
    private const ERROR_QP_MISMATCH = 105;

    private const SUCCESS = 200;
    private const SUCCESS_MATCHED = 201;

}
