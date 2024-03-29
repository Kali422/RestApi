<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\Runner;

final class ResultCacheExtension implements AfterIncompleteTestHook, AfterLastTestHook, AfterRiskyTestHook, AfterSkippedTestHook, AfterSuccessfulTestHook, AfterTestErrorHook, AfterTestFailureHook, AfterTestWarningHook
{
    /**
     * @var TestResultCacheInterface
     */
    private $cache;

    public function __construct(TestResultCache $cache)
    {
        $this->cache = $cache;
    }

    public function executeAfterSuccessfulTest(string $test, float $time): void
    {
        $testName = $this->getTestName($test);

        $this->cache->setTime($testName, \round($time, 3));
    }

    /**
     * @param string $test A long description format of the current test
     *
     * @return string The test name without TestSuiteClassName:: and @dataprovider details
     */
    private function getTestName(string $test): string
    {
        $matches = [];

        if (\preg_match('/^(?<name>\S+::\S+)(?:(?<dataname> with data set (?:#\d+|"[^"]+"))\s\()?/', $test, $matches)) {
            $test = $matches['name'] . ($matches['dataname'] ?? '');
        }

        return $test;
    }

    public function executeAfterIncompleteTest(string $test, string $message, float $time): void
    {
        $testName = $this->getTestName($test);

        $this->cache->setTime($testName, \round($time, 3));
        $this->cache->setState($testName, BaseTestRunner::STATUS_INCOMPLETE);
    }

    public function executeAfterRiskyTest(string $test, string $message, float $time): void
    {
        $testName = $this->getTestName($test);

        $this->cache->setTime($testName, \round($time, 3));
        $this->cache->setState($testName, BaseTestRunner::STATUS_RISKY);
    }

    public function executeAfterSkippedTest(string $test, string $message, float $time): void
    {
        $testName = $this->getTestName($test);

        $this->cache->setTime($testName, \round($time, 3));
        $this->cache->setState($testName, BaseTestRunner::STATUS_SKIPPED);
    }

    public function executeAfterTestError(string $test, string $message, float $time): void
    {
        $testName = $this->getTestName($test);

        $this->cache->setTime($testName, \round($time, 3));
        $this->cache->setState($testName, BaseTestRunner::STATUS_ERROR);
    }

    public function executeAfterTestFailure(string $test, string $message, float $time): void
    {
        $testName = $this->getTestName($test);

        $this->cache->setTime($testName, \round($time, 3));
        $this->cache->setState($testName, BaseTestRunner::STATUS_FAILURE);
    }

    public function executeAfterTestWarning(string $test, string $message, float $time): void
    {
        $testName = $this->getTestName($test);

        $this->cache->setTime($testName, \round($time, 3));
        $this->cache->setState($testName, BaseTestRunner::STATUS_WARNING);
    }

    public function executeAfterLastTest(): void
    {
        $this->flush();
    }

    public function flush(): void
    {
        $this->cache->persist();
    }
}
