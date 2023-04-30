<?php

class Order
{
    private int $id;
    private string $firstname;
    private string $lastname;
    private string $pigeonnumber;
    private string $address;

    private array $beers;

    public function __construct(int $id, string $firstname, string $lastname, string $pigeonnumber, string $address, array $beers = [])
    {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->pigeonnumber = $pigeonnumber;
        $this->address = $address;
        $this->beers = $beers;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getPigeonnumber(): string
    {
        return $this->pigeonnumber;
    }

    /**
     * @param string $pigeonnumber
     */
    public function setPigeonnumber(string $pigeonnumber): void
    {
        $this->pigeonnumber = $pigeonnumber;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return array
     */
    public function getBeers(): array
    {
        return $this->beers;
    }

    /**
     * @param array $beers
     */
    public function setBeers(array $beers): void
    {
        $this->beers = $beers;
    }

    public static function from_data(mixed $row): Order
    {
        if (!array_key_exists('orderid', $row) ||
            !array_key_exists('firstname', $row) ||
            !array_key_exists('lastname', $row) ||
            !array_key_exists('pigeonnumber', $row) ||
            !array_key_exists('address', $row))
            internal_error('Unable to parse Order');

        return new Order(
            intval($row['orderid']),
            $row['firstname'],
            $row['lastname'],
            $row['pigeonnumber'],
            $row['address']
        );
    }

    public static function from_view(): Order
    {
        list($firstname, $lastname, $pigeonnumber, $address, $beers, $id) = json_body(['firstname', 'lastname', 'pigeonnumber', 'address', 'beers'], ['id']);

        $id = verify_number($id, allow_null: true);
        $firstname = verify_text($firstname, 1, 45);
        $lastname = verify_text($lastname, 1, 45);
        $pigeonnumber = verify_text($pigeonnumber, 1, 45);
        $address = verify_text($address, 1, 128);

        if (!is_array($beers))
            bad_request('beers must be an array');

        foreach ($beers as $beer_id => $amount) {
            $beer_id = verify_number($beer_id);
            $amount = verify_number($amount);
            $beers[$beer_id] = $amount;
        }

        return new Order(
            intval($id),
            $firstname,
            $lastname,
            $pigeonnumber,
            $address,
            $beers
        );
    }

    public function to_view(): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'pigeonnumber' => $this->pigeonnumber,
            'address' => $this->address,
            'beers' => $this->beers
        ];
    }
}