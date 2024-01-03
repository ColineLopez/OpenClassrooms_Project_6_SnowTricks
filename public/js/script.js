document.getElementsById('deleteLink').addEventListener('click', function(event) {
      event.preventDefault(); // Empêche le comportement par défaut du lien (naviguer vers une autre page)

      if (confirm('Êtes-vous sûr de vouloir supprimer ?')) {
        // Si la confirmation est acceptée, vous pouvez rediriger vers la route delete ici
        // window.location.href = '/votre-route-de-suppression';
        console.log('Suppression effectuée !');
      } else {
        console.log('Suppression annulée.');
      }
    });