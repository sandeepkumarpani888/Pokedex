<?hh

class pokeData implements JsonSerializable{
	private string $name;
	private string $weight;
	private string $height;
	private string $spritesFront_Female;
	private string $spritesFrontDefault;

	public function __construct() {
		$this->name = 'error';
		$this->weight = 'error';
		$this->height = 'error';
		$this->spritesFront_Female = 'error';
		$this->spritesFrontDefault = 'error';
	}

	public function setName(string $name) {
		$this->name = $name;
	}

	public function setWeight(string $weight) {
		$this->weight = $weight;
	}

	public function setHeight(string $height) {
		$this->height = $height;
	}

	public function setSpritesFront_Female(string $spritesFront_Female) {
		if(! is_null($spritesFront_Female)) {
			$this->spritesFront_Female = $spritesFront_Female;
		}
	}

	public function setSpritesFrontDefault(string $spritesFrontDefault) {
		if(! is_null($spritesFrontDefault)) {
			$this->spritesFrontDefault = $spritesFrontDefault;
		}
	}

	public function getName(): string {
		return $this->name;
	}

	public function getSprties(): string {
		return $this->spritesFrontDefault;
	}

	public function jsonSerialize() {
		return [
			'name' => $this->name,
			'weight' => $this->weight,
			'height' => $this->height,
			'spritesFront_Female' => $this->spritesFront_Female,
			'spritesFrontDefault' => $this->spritesFrontDefault
		];
	}
}

//retrive data from pokemon-api
async function getPokeDataFromAPI(string $pokemonName): Awaitable<pokeData> {
	$cl = await \HH\Asio\curl_exec('http://pokeapi.co/api/v2/pokemon/' . $pokemonName .'/');
	$res = json_decode($cl);
	$pokemonData = new pokeData();
	$pokemonData->setName($res->name);
	$pokemonData->setHeight((string)$res->weight);
	$pokemonData->setWeight((string)$res->height);
	if(! is_null($res->sprites->front_female)) {
		$pokemonData->setSpritesFront_Female($res->sprites->front_female);
	}
	if(! is_null($res->sprites->front_default)) {
		$pokemonData->setSpritesFrontDefault($res->sprites->front_default);
	}
	return $pokemonData;
}


async function saveInRedis(pokeData $data): Awaitable<string> {
	try{
		$redis = new Redis();
		$redis->connect('127.0.0.1', 6379);
		$redis->set($data->getName(), json_encode($data));
		return '200';
	}
	catch (Exception $e){
		return '500';
	}
}

async function saveInSQL(pokeData $data): Awaitable<string> {
	try {
		return '200';
	}
	catch (Exception $e){
		return "500";
	}
}

function checkSuccess(Map<string, string> $res): string{
	if($res['redis']=='200' && $res['sql']=='200'){
		return '200';
	}
	else{
		return '500';
	}
}

//save in redis + SQL
async function saveInDB(string $pokemonName): Awaitable<string> {
	$data = \HH\Asio\join(getPokeDataFromAPI($pokemonName));
	$awaitables = array(
		'redis' => saveInRedis($data),
		'sql' => saveInSQL($data)
	);
	$results = await \HH\Asio\m($awaitables);
	return checkSuccess($results);
}

//get the data back
function retrivePokeData(string $pokemonName): string {
	try {
		$redis = new Redis();
		$redis->connect('127.0.0.1', 6379);
		if($redis->exists($pokemonName)) {
			$pokeDataJSON = $redis->get($pokemonName);
			$pokeDataJSON = json_decode($pokeDataJSON);
			$pokeDataJSON = json_encode($pokeDataJSON);
			return $pokeDataJSON;
		}
		else{
			$pokeData = \HH\Asio\join(getPokeDataFromAPI($pokemonName));
			$status = saveInRedis($pokeData);
			$status->getWaitHandle()->join();
			return json_encode($pokeData);
		}
	} catch(Exception $e) {
		$data = new pokeData();
		return json_encode($data);
	}
}
