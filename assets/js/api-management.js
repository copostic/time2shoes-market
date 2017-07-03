function getArticles(page, pagesize, category) {
    //Set main container variable
    const mainContainer = document.querySelector('.container');

    //Clear page
    articles = document.querySelectorAll('.article');
    for (const article of articles) {
        article.parentElement.removeChild(article);
    }

    window._fetch = (url) => {

        return new Promise((resolve, reject) => {
            const req = new XMLHttpRequest()
            req.open("GET", url, true)

            req.onload = () => {
                if (req.status == 200) {
                    resolve(req.responseText, url)
                } else
                    reject(req.responseText, url)
            }

            req.send()
        })

    }
    _fetch("https://api.zalando.com/articles?page=" + page + "&pageSize=" + pagesize + "&category=" + category)
        .then(result => JSON.parse(result))
        .then(obj => {

            for (article of obj.content) {
                console.log(article)

                const articleDiv = document.createElement('div')
                articleDiv.setAttribute('class', 'article col-md-2')

                const divName = document.createElement('div')
                divName.setAttribute('class', 'name')
                divName.innerHTML = "<a href='" + article.shopUrl + "'target='_blank'>" + article.name + "</a>"
                const img = document.createElement('img')
                img.setAttribute('src', article.media.images[0].mediumHdUrl)

                const divPrice = document.createElement('div')
                divPrice.setAttribute('class', 'price')
                divPrice.innerHTML = "Prix: " + article.units[0].price.value + "€" + "<a  style=\" color: grey; position:relative; float:right;margin-right:5%;\"href=\"panier.php?action=ajout&amp;l=" + article.name + "&amp;q=1&amp;p=" + article.units[0].price.value + "&amp;id=" + article.id + "\" onclick=\"window.open(this.href, '', 'toolbar=no, location=no, directories=no, status=yes, scrollbars=yes, resizable=yes, copyhistory=no, width=600, height=350'); return false;\"><span class=\"glyphicon glyphicon-shopping-cart\" </span></a>"

                articleDiv.appendChild(divName)
                articleDiv.appendChild(img)
                articleDiv.appendChild(divPrice)
                mainContainer.appendChild(articleDiv)

            }


        })
        .catch(err => console.error(err))
}