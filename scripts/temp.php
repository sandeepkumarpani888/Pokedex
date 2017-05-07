<?hh

class data implements JsonSerializable{
	private string $name;
	private string $life;

	public function __construct(string $name){
		$this->name = $name;
		$this->life = "sandeep";
	}

	public function jsonSerialize() {
		return [
			'name' => $this->name,
			'life' => $this->life
		];
	}
};

function save(string $name): void {
	$newData=new data($name);
	echo json_encode($newData);
}

function justDoIt(): void {
	echo "yolo";
}

async function testCurl($name): Awaitable<void> {
	$cl = await \HH\Asio\curl_exec('http://pokeapi.co/api/v2/pokemon/' . $name .'/');
	$res = json_decode($cl);
	var_dump($res);
}

async function asynCurl(): Awaitable<void> {
	$awaitables = array(
		'mew' => testCurl('mew'),
		'charmander' => testCurl('charmander')
	);
	$results = await \HH\Asio\m($awaitables);
}

function getData($name) {
	$redis = new Redis();
	$redis->connect('127.0.0.1',6379);
	$redis->set("mew","moew");
	echo $redis->get("mew");
}

save("sandeep");
justDoIt();
getData('mew');
// \HH\Asio\join(asynCurl());