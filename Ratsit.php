<?php
namespace livijn\Ratsit;

use GuzzleHttp\Client;
use livijn\Ratsit\Model\SearchResult;

class Ratsit
{
    private $client;
    private $options = [];
    private $denormalizer;

    const DEFAULT_OPTIONS = [
        'url' => 'https://api.checkbiz.se/api/v1/',
        'token' => '',
    ];

    public function __construct(string $token, array $options = [])
    {
        $options['token'] = $token;
        $this->options = array_merge(self::DEFAULT_OPTIONS, $options);
        $this->client = new Client;
        $this->denormalizer = new Denormalizer;
    }

    /**
     * @param string $method
     * @param string $package
     * @param array $parameters
     * @return mixed
     */
    private function request(string $method, string $package, array $parameters = [])
    {
        return $this->client->request(
            'GET',
            $this->options['url'] . $method . '?' . http_build_query($parameters),
            ['headers' => [
                'Authorization' => sprintf('Basic %s', $this->options['token']),
                'package' => $package,
            ]]
        );
    }

    /**
     * @param string|null $ssn
     *
     * @return Model\Person
     */
    public function findPersonBySocialSecurityNumber(?string $ssn)
    {
        $json = $this->request('personinformation', 'personadress', ['ssn' => $ssn])->getBody()->getContents();

        var_dump($json);

        return $this->denormalizer->denormalizerPersonInformation(json_decode($json, true));
    }

    public function findAmountOfDogsBySocialSecurityNumber(string $ssn)
    {
        $json = $this->request('personinformation', 'hundpaadress', ['ssn' => $ssn])->getBody()->getContents();

        return $this->denormalizer->denormalizerDogsAtAddress(json_decode($json, true));
    }

    /**
     * @param null|string $who
     * @param null|string $where
     *
     * @return Model\Person[]|SearchResult
     */
    public function searchPerson(?string $who, ?string $where = null)
    {
        $json = $this->request('personsearch', 'personsok', ['who' => $who, 'where' => $where])->getBody()->getContents();

        $searchResult = $this->getDenormalizer()->denormalizerPersonSearch(json_decode($json, true));

        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(
                PersonSearchResultEvent::NAME, new PersonSearchResultEvent($searchResult)
            );
        }

        return $searchResult;
    }
}
