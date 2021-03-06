<?php

namespace LeagueWrap\Api;

class Matchlist extends AbstractApi
{
    /**
     * Valid version for this api call.
     *
     * @var array
     */
    protected $versions = [
        'v3',
    ];

    /**
     * A list of all permitted regions for the Champion api call.
     *
     * @param array
     */
    protected $permittedRegions = [
        'br',
        'eune',
        'euw',
        'lan',
        'las',
        'na',
        'oce',
        'kr',
        'ru',
        'tr',
        'jp'
    ];

    /**
     * The amount of time we intend to remember the response for.
     *
     * @var int
     */
    protected $defaultRemember = 1800;

    /**
     * @return string domain used for the request
     */
    public function getDomain()
    {
        return "{$this->getRegion()->getStandardizedDomain()}match/";
    }

    /**
     * Get the match list by summoner identity.
     *
     * @param int|\LeagueWrap\Dto\Summoner  $identity    This is the accountId of the summoner or the summoner itself!
     * @param array|string|null             $rankedQueues List of ranked queue types to use for fetching games.
     * @param array|string|null             $seasons      List of seasons to use for fetching games.
     * @param array|string|null             $championIds  Comma-separated list of champion IDs to use for fetching games.
     * @param int|null                      $beginIndex   The begin index to use for fetching games.
     * @param int|null                      $endIndex     The end index to use for fetching games.
     * @param int|null                      $beginTime    The begin time for fetching games in milliseconds
     * @param int|null                      $endTime      The end time for fetching games in milliseconds
     *
     * @throws \LeagueWrap\Exception\CacheNotFoundException
     * @throws \LeagueWrap\Exception\InvalidIdentityException
     * @throws \LeagueWrap\Exception\RegionException
     *
     * @return \LeagueWrap\Dto\MatchList
     */
    public function matchlist($identity, $rankedQueues = null, $seasons = null, $championIds = null, $beginIndex = null, $endIndex = null, $beginTime = null, $endTime = null)
    {
        $accountId = $this->getAccountId($identity);

        $requestParamas = $this->parseParams($rankedQueues, $seasons, $championIds, $beginIndex, $endIndex, $beginTime, $endTime);
        $array = $this->request('matchlists/by-account/'.$accountId, $requestParamas);
        $matchList = $this->attachStaticDataToDto(new \LeagueWrap\Dto\MatchList($array));

        $this->attachResponse($identity, $matchList, 'matchlist');

        return $matchList;
    }

    protected function getAccountId($identity)
    {
        $accountId = $identity;
        if($identity instanceof \LeagueWrap\Dto\Summoner) {
            $accountId = $identity->accountId;
        }

        if (!((is_string($accountId) || is_numeric($accountId)) && ctype_digit((string)$accountId))) {
            throw new \InvalidArgumentException(
                "the given accountId must be an integer (accountId) ".gettype($accountId)." given"
            );
        }
        return $accountId;
    }

    /**
     * Parse the params into an array.
     *
     * @param mixed $rankedQueues
     * @param mixed $seasons
     * @param mixed $championIds
     * @param mixed $beginIndex
     * @param mixed $endIndex
     * @param mixed $beginTime
     * @param mixed $endTime
     *
     * @return array
     */
    protected function parseParams($rankedQueues = null, $seasons = null, $championIds = null, $beginIndex = null, $endIndex = null, $beginTime = null, $endTime = null)
    {
        $params = [];

        if (isset($rankedQueues)) {
            if (is_array($rankedQueues)) {
                $params['queue'] = array_values($rankedQueues);
            } else {
                $params['queue'] = $rankedQueues;
            }
        }
        if (isset($seasons)) {
            if (is_array($seasons)) {
                $params['season'] = array_values($seasons);
            } else {
                $params['season'] = $seasons;
            }
        }

        if (isset($championIds)) {
            if (is_array($championIds)) {
                $params['champion'] = array_values($championIds);
            } else {
                $params['champion'] = $championIds;
            }
        }

        if (isset($beginIndex)) {
            $params['beginIndex'] = $beginIndex;
        }
        if (isset($endIndex)) {
            $params['endIndex'] = $endIndex;
        }
        if (isset($beginTime)) {
            $params['beginTime'] = $beginTime;
        }
        if (isset($endTime)) {
            $params['endTime'] = $endTime;
        }

        return $params;
    }

    /**
     * @param int|\LeagueWrap\Dto\Summoner  $identity    This is the accountId of the summoner or the summoner itself!
     *
     * @throws \LeagueWrap\Exception\RegionException
     * @throws \InvalidArgumentException
     *
     * @return \LeagueWrap\Dto\MatchList
     */
    public function recent($identity)
    {
        $accountId = $this->getAccountId($identity);
        $array = $this->request("matchlists/by-account/{$accountId}/recent");
        $matchList = $this->attachStaticDataToDto(new \LeagueWrap\Dto\MatchList($array));

        $this->attachResponse($identity, $matchList, 'matchlist');
        return $matchList;
    }
}
