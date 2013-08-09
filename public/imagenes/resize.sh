#!/bin/bash
#defecto

lista_plato='plato.txt'
tamano_plato_principal='407x272>'
tamano_plato_destacado='215x155>'
tamano_plato_general='145x112>'
tamano_restaurante='240x143>'
#plato
cd plato   
#find . -name "*jpg" > $lista_plato
for file in $(find . -name "*jpg")
do
    if [[ -f $file ]]; then
        echo $file
        substr=${file:2:3}
        case $substr in
        'des')
#             echo 'des';;             
            convert -resize $tamano_plato_destacado $file $file;;
        'gen')
#             echo 'gen';;
            convert -resize $tamano_plato_general $file $file;;
        'pri')
#             echo 'pri';;
            convert -resize $tamano_plato_principal $file $file;;
        esac
        
#         if [ $substr = 'des' ]; then 
#             convert -resize tamano_plato_destacado $file $file
#         fi
        #convert -resize tamano_plato_principal $file $file
        #convert -resize tamano_plato_destacado $file $file
        #convert -resize tamano_plato_general $file $file
        #copy stuff ....
    fi
done


#restaurante