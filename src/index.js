// @flow
import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

class PokemonRender extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return(
            <div className="pokemon">
                <b>{this.props.name}</b>
                <img src={this.props.imageUrl} alt="HTML5 Icon"/>
            </div>
        );
    }
}

class RenderPokemonList extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        let renderItems= [];
        this.props.listOfPokemon.forEach((pokemon) => {
            renderItems.push(<PokemonRender key={pokemon.name} name={pokemon.name} imageUrl={pokemon.spritesFrontDefault} />);
        });

        return (
            <li>
                {renderItems}
            </li>
        );
    }
}

class SearchField extends React.Component {
    constructor(props) {
        super(props);
        this.handleChange = this.handleChange.bind(this);
    }

    handleChange(event) {
        this.props.onValueChange(event.target.value);
    }

    render() {
        const value = this.props.value;
        return (
            <fieldset>
                <legend> 'Enter pokemon name in search box' </legend>
                <input value={value} onChange={this.handleChange} />
                <button className="addPokemon" 
                        onClick={this.props.addPokemon}>Submit</button>
            </fieldset>
        );
    }
}

class ControlUnit extends React.Component {
    constructor(props) {
        super(props);
        this.onSearchQueryChange = this.onSearchQueryChange.bind(this);
        this.addPokemon = this.addPokemon.bind(this);
        this.state = {
            currSearchQuery: '',
            pokemonList: [],
        };
    }

    addPokemon() {
        const pokemonName = this.state.currSearchQuery;
        const url = 'http://localhost:8080/helper.php/getData/' + pokemonName;
        axios.get(url)
            .then((res) => {
                console.log(res);
                let data = res.data;
                console.log(data);
                if(data.name !== 'error'){
                    let pokeList = this.state.pokemonList;
                    pokeList.push(data);
                    this.setState({
                        pokemonList: pokeList
                    });
                }
            })
    }

    onSearchQueryChange(query) {
        this.setState({
            currSearchQuery: query
        });
    }

    render() {
        const searchQuery = this.state.currSearchQuery;
        const renderList=[];
        this.state.pokemonList.forEach((pokemon) => {
            if(pokemon.name.startsWith(searchQuery)) {
                renderList.push(pokemon);
            }
        });

        return (
            <div className="controlUnit">
                <SearchField value={searchQuery} onValueChange={this.onSearchQueryChange} addPokemon={this.addPokemon}/>
                <RenderPokemonList listOfPokemon={renderList} />
            </div>
        );
    }
}

ReactDOM.render(
    <ControlUnit />,
    document.getElementById('root')
);