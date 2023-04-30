<?php

class Beer
{
    private int $id;
    private string $name;
    private string $type;
    private int $alcohol;
    private int $price;
    private int $mark;
    private ?string $description;
    private ?string $imagePath;
    private int $stock;

    public function __construct(int $id, string $name, string $type, int $alcohol, int $price, int $mark, ?string $description, ?string $imagePath, int $stock)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->alcohol = $alcohol;
        $this->price = $price;
        $this->mark = $mark;
        $this->description = $description;
        $this->imagePath = $imagePath;
        $this->stock = $stock;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getAlcohol(): int
    {
        return $this->alcohol;
    }

    /**
     * @param int $alcohol
     */
    public function setAlcohol(int $alcohol): void
    {
        $this->alcohol = $alcohol;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getMark(): int
    {
        return $this->mark;
    }

    /**
     * @param int $mark
     */
    public function setMark(int $mark): void
    {
        $this->mark = $mark;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    /**
     * @param string|null $imagePath
     */
    public function setImagePath(?string $imagePath): void
    {
        $this->imagePath = $imagePath;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @param int $stock
     */
    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }

    public static function from_data(mixed $row): Beer
    {
        if (!array_key_exists('beerid', $row) ||
            !array_key_exists('name', $row) ||
            !array_key_exists('type', $row) ||
            !array_key_exists('alcohol', $row) ||
            !array_key_exists('price', $row) ||
            !array_key_exists('mark', $row) ||
            !array_key_exists('description', $row) ||
            !array_key_exists('imagepath', $row) ||
            !array_key_exists('stock', $row))
            internal_error('Unable to parse Beer');

        return new Beer(
            intval($row['beerid']),
            $row['name'],
            $row['type'],
            intval($row['alcohol']),
            intval($row['price']),
            intval($row['mark']),
            $row['description'],
            $row['imagepath'],
            intval($row['stock'])
        );
    }

    public static function from_view(): Beer
    {
        list($id, $name, $type, $alcohol, $price, $mark, $description, $imagePath, $stock) = json_body(['id', 'name', 'type', 'alcohol', 'price', 'mark'], ['description', 'imagePath', 'stock']);

        if ($id !== null)
            $id = verify_number($id);

        $name = verify_text($name, 1, 45);
        $type = verify_text($type, 1, 45);
        $alcohol = verify_number($alcohol);
        $price = verify_number($price);
        $mark = verify_number($mark, allow_zero: true);
        $description = verify_text($description, 0, 65565, true, true);
        $imagePath = verify_text($imagePath, 0, 128, true, true);
        $stock = verify_number($stock ?? 0, allow_zero: true);

        return new Beer(
            intval($id),
            $name,
            $type,
            intval($alcohol),
            intval($price),
            intval($mark),
            $description,
            $imagePath,
            intval($stock)
        );
    }

    public function to_view(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'alcohol' => $this->alcohol,
            'price' => $this->price,
            'mark' => $this->mark,
            'description' => $this->description,
            'imagePath' => $this->imagePath,
            'stock' => $this->stock
        ];
    }
}