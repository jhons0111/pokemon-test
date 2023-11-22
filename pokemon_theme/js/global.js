(function ($, Drupal, drupalSettings) {
    'use strict';
  
    Drupal.behaviors.PokemonBehavior = {
      attach: function (context, settings) {

        // Delegate events to the document if the element clicked contains the class .saveFavorite
        document.addEventListener('click', function (event) {
          if (event.target.classList.contains('saveFavorite')) {
            const pokemonId = event.target.dataset.pokemonid;
            const pokemonPicture = event.target.dataset.pokemonpicture;
            const pokemonName = event.target.dataset.pokemonname;
        
            saveFavoritesPokemons(pokemonId, pokemonPicture, pokemonName)
              .then(res => {

                const message_after_send = document.createElement('h5');
                if(res.code == 409 && res.error_message == 'pokemon_exist'){

                  //We disable the button 'add to favorites'
                  event.target.classList.remove('saveFavorite');
                  event.target.classList.add('saveFavoriteDisabled');

                  //If the user attempts to save a pokemons that alredy exists, we insert an error message below the button.
                  message_after_send.textContent = res.error;
                  message_after_send.classList.add('favorites-message');
                  event.target.insertAdjacentElement('afterend', message_after_send);
                }else if(res.code == 409 && res.error_message == 'more_than_10'){

                  //We disable the button 'add to favorites'
                  event.target.classList.remove('saveFavorite');
                  event.target.classList.add('saveFavoriteDisabled');

                  //If the user attempts to save more than 10 Pokémon, we insert an error message below the button.
                  message_after_send.textContent = res.error;
                  message_after_send.classList.add('favorites-message');
                  event.target.insertAdjacentElement('afterend', message_after_send);
                }else if(res.code == 201){

                  //We disable the button 'add to favorites' and show to the user the correct message
                  event.target.textContent = 'Pokemon added';
                  event.target.classList.remove('saveFavorite');
                  event.target.classList.add('saveFavoriteDisabled');

                  message_after_send.textContent = res.message;
                  message_after_send.classList.add('favorites-message-success');
                  event.target.insertAdjacentElement('afterend', message_after_send);
                };
              });
          }
        });

        //Returns every card with pokemon's data in the home page
        const template = (pokemon_name, pokemon_url, id)=>{
          const createDiv = document.createElement('div');
          createDiv.classList.add('pokemon-card');
          createDiv.setAttribute('data-request', pokemon_url);
          const html = `
            <picture>
              <img src="https://elcomercio.pe/resizer/Hl0FRfSg8HtgIcJpId89TYMZyY4=/640x0/smart/filters:format(jpeg):quality(75)/cloudfront-us-east-1.images.arcpublishing.com/elcomercio/P7KP35OZHRAKTGNKQU4R5ANHIU.jpg">
            </picture>
            <p>${pokemon_name}</p>
            <a data-request="${pokemon_url}">Conoce más</a>     
            <input type="checkbox" id="${id}" name="${id}">     
            <div class="compare">
              <a>Compare</a>
            </div>
          `;
          createDiv.innerHTML = html;

          return createDiv;
        }

        //Returns every card with pokemon's data in the home page
        const templateForTable = (pokemon_name, abilities, picture, pokemon_attacks, id)=>{

          const createDiv = document.createElement('div');
          createDiv.classList.add('pokemon-card');

          //Set the abilities
          let list_of_abilities = '';
          abilities.forEach((pokemon)=>{

            list_of_abilities += `<li>${pokemon.ability.name}</li>`;
          })

          //Set the attacks
          let list_of_attacks = '';
          pokemon_attacks.forEach((pokemon)=>{

            list_of_attacks += `<li>${pokemon.move.name}</li>`;
          })

          var addToFavorites = '';
          if(drupalSettings.user.uid == 0){

            addToFavorites = `<a class="saveFavoriteDisabled">Add to favorites</a><h5>Sign in o sign up to add to favorites</h5>`;
          }else{

            addToFavorites = `<a class="saveFavorite" data-pokemonid="${id}" data-pokemonpicture="${picture.front_default}" data-pokemonname="${pokemon_name}">Add to favorites</a>`;
          }
          //Set the pokemon's card
          const html = `<picture>
                <img src="${picture.front_default}">
            </picture>
            <p>${pokemon_name}</p>
            <div class="pokemon-card-characteristics">
                <p>Abilities</p>
                <ul>
                  ${list_of_abilities}
                </ul>
            </div>
            <div class="pokemon-card-characteristics">
                <p>Attacks</p>
                <ul>
                  ${list_of_attacks}
                </ul>
            </div>
            ${addToFavorites}`;
          createDiv.innerHTML = html;

          return createDiv;
        }

        /**
         * 
         * Function for request all pokemons data
         * 
         * @param {string} url 
         * @returns promise
         */
        const requestPokemon = async(url)=>{
          const request = await fetch(url ,{
            method: "GET"
          });
          const parseRequest = await request.json();

          return parseRequest;
        }

        /**
         * 
         * Function for request pokemon's data
         * 
         * @param {string} url 
         * @returns promise
         */
        const saveFavoritesPokemons = async(pokemonId, pokemonPicture, pokemonName)=>{

          const request = await fetch('/api/save-favorite-pokemon' ,{
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              'pokemon_id': pokemonId,
              'picture': pokemonPicture,
              'pokemon_name': pokemonName
            }),
          })
          const parseRequest = await request.json();

          return parseRequest;
        }

        /**
         * 
         * Function for save favorites pokemons
         * 
         * @param {string} url 
         * @returns promise
         */
        const requestPokemonData = async(url)=>{
          const request = await fetch(url ,{
            method: "GET"
          });
          const parseRequest = await request.json();

          return parseRequest;
        }

        //Conext to endpoint
        requestPokemon('https://pokeapi.co/api/v2/pokemon/?offset=15&limit=15')
          .then(res=>{

            const { results } = res;
            const getCtnToRender = document.querySelector('#render-pokemon');
            //Render data
            results.forEach((data, indice)=>{
              const { name:pokemon_name, url:pokemon_url } = data;
              const templatePokemon = template(pokemon_name, pokemon_url, indice);
              getCtnToRender.append(templatePokemon);
            });
            //Checkbox event
            const getAllCheckbox = document.querySelectorAll('#render-pokemon input');
            getAllCheckbox.forEach((check)=>{
              check.addEventListener('click', ()=>{
                const getParent = check.closest('.pokemon-card');
                getParent.classList.toggle('validate');
                const validateLength = document.querySelectorAll('.pokemon-card.validate');
                const getAllitems = document.querySelectorAll('#render-pokemon .pokemon-card');
                getAllitems.forEach((item)=>{
                  item.classList.remove('active'); 
                });
                if(validateLength.length > 1 && validateLength.length <= 2){
                  getAllitems.forEach((item)=>{
                    item.classList.contains('validate') ? item.classList.add('active') : item.classList.remove('active'); 
                  });
                }
              });
            });
            //Functionality to compare pokemons
            const getAllButtons = document.querySelectorAll('.compare');
            getAllButtons.forEach((btn)=>{
              btn.addEventListener('click', ()=>{
                const getAllItems = document.querySelectorAll('.pokemon-card.validate');
                getAllItems.forEach((item)=>{

                  //Show comparative table
                  document.getElementById('wrapper-of-modal').style.visibility = 'visible';

                  //Render pokemon's data in the comparative table
                  requestPokemonData(item.dataset.request)
                  .then(res=>{
                    const getTableToRender = document.querySelector('#render-pokemon-table');

                    const { name:pokemon_name, abilities:abilities, sprites:picture, moves:pokemon_attacks, id:id } = res;

                      const PokemonCard = templateForTable(pokemon_name, abilities, picture, pokemon_attacks, id);
                      getTableToRender.append(PokemonCard);
                  })
                });
              });
            })
          }).catch(err=>{ console.log('Something was wrong with the render', err) })
        }
      }
  })(jQuery, Drupal, drupalSettings);