$(document).ready(function(){
  // TABLEAU MODIFICATION RESERVATION CHAMBRE
    // On cache par défaut la section ainsi que la table
    //  des résultats tant que la requête AJAX n'a pas été
    //  accomplie.
    //$("#checkIn h3").hide();
    $("#room").hide();
    $("#room table").hide();

    function sendVerif(e) {
      const checkIn = $("#fromDate").val();
      const checkOut = $("#toDate").val();

        e.preventDefault();
           $.post( "profile.php",
                   { CheckIn: checkIn,
                     CheckOut: checkOut,
                     idCategorieChambre: $("#idCategorieChambre").val(),
                     idReservation: $("#idReservation").val(),
                     idChambre: $("#idChambre").val(),
                     verify: $("#verify").val()
                   },

                   function(data, status) {
                    // On transforme la chaîne de caractères
                    //   en objet JSON.
                     let json = JSON.parse(data);

                     // Dès l'arrivée d'un résultat, on affiche la section
                     //   mais pas la table des résultats.


                     // Si la date d'arrivée et/ou date de départ n'est pas remplis
                     // alors on affiche une message d'erreur
                     if ( checkIn.trim().length == 0 || checkOut.trim().length == 0)
                     {
                       $("#checkIn h3").text("Veuillez entrez la date d'arrivée et la date de départ svp");
                     }

                     if (json.price < 0)
                     {
                       $("#room table").hide()
                       $("#checkIn h3").text("Désolé, nous n'avons pas de disponibilité pendant ces dates")

                     }
                     else if (json.price > 0){
                       // Si dans les données JSON, il est indiqué que le prix
                       //   n'est pas nulle, alors on cache le message indiquant
                       //   qu'aucune réservation n'est disponible et on afficher
                       //   la table des réservations disponibles.
                       //$("#room").show();
                       $("#room").show()
                       $("#room table").show()
                       $("#room h3").hide()
                     }
                     // On contruit enfin le corps du tableau avec les données
                     //   de la réservation.
                     $("#tabBody").html(
                       `<tr>
                           <td>${json.dateArriver}</td>
                           <td>${json.dateDepart}</td>
                           <td>${json.price}</td>
                           <td>
                             <form class='' action='profile.php' method='post'>
                               <input type='hidden' name='CheckIn' value='${json.dateArriver}'>
                               <input type='hidden' name='CheckOut' value='${json.dateDepart}'>
                               <input type='hidden' name='idReservation' value='${json.idReservation}'>
                               <input type='hidden' name='idChambre' value='${json.idChambre}'>
                               <input type='hidden' name='updateResa' value='1'>
                               <input type='submit' id='update' value='Valider la modification'>
                             </form>
                           </td>
                       </tr>`)

          });

    };
    $('#verifier').click(sendVerif);

      // Insérer Commentaire

  		// si le formulaire est correct, retourne 'true'
  		// sinon affiche le message adéquat
  		function checkForm()
  		{
  			// Vérification des champs formulaires
  			const title = $( "input[name = 'title']" ).val();
  		  const comment = $( "textarea[name = 'comment']" ).val();
  		  const note = $( "input[name = 'note']" ).val();

  			if ( title.trim().length == 0)
  			{
  				$("#errorTitle").text("Le titre est invalide ou laissé vide");
  				return false;
  			}
  			if ( isNaN( note ) || note>5)
  			{
  				$("#errorTitle").text("");
  				$("#errorNote").text("Veuillez saisir une note comprise entre 1 et 5");
  				return false;
  			}
  			if ( comment.trim().length == 0)
  			{
  				$("#errorNote").text("");
  				$("#errorComment").text("L'avis est invalide ou laissé vide");
  				return false;
  			}

        else {
            $("#errorComment").text("");
            return true;
          }


  		}
  		// réinitialise le formulaire
  		function clearForm()
  		{
  			$( "textarea" ).val( "" );
  			$( "input[type = \"text\"]" ).val( "" );
  		}

  		// si les données du formulaire sont correctes
  		$( "#publish" ).click( function(e)
  		{
  	      e.preventDefault();
  				if ( checkForm() )
  				{
  					// On envoie les données du formulaire via une
  					//	requête de type POST.
  					$.post( "profile.php",
  						{
  							// Titre
  							title: $( "input[name = 'title']" ).val(),

  							// Note
  							note: $( "input[name = 'note']" ).val(),

  							// Commentaire
  							comment: $( "textarea[name = 'comment']" ).val(),

  						},
              function(data, status) {
                let info = JSON.parse(data)
                $( "#publish" ).replaceWith( // on remplace le button avec un paragraphe
                //  console.log(info)
                   `<p class = '${info.messageStyle}'>${info.messageComment}</p>`
                )
              }
            )
  					// On supprime les données du formulaire seulement
  					//	si toutes les données ont été validées.
  					clearForm();
  				}

  		} );
});
