/*
	htmlTable
	Provides a standard interface for building html tables and adding them to the DOM purely with javascript.

	Input:
		conf - object - a config object that describes how the table will be built, header row, ids, classes and the 
			ability to map data to its respective column
			
			Structure:
				{
					table:
					{
						id: string,														// id of the table
					},
					header:
					{
						id: string														// id of table header 
					},
					body:{
						id:string,														// id of the table body element
						rows:															// Specifc tr level attributes
						{
							{attribute}: string value || function that returns a string
						}
					},
					columns:															// Specifc header columns and maps data to the column
					[
						{
							label: string,												// Header text
							data:														// Data mapping, can specific class and value mappings
							{
								class: string || function that returns a string,
								value: string property || function that returns a value
							}
						},
						...
					],
					pagination:												// Not needed but I would like it
					{
						chunks: 50,
					}
				}
	Needs to
		Add header
		Add rows from an array
		bind eventhandlers
		
	Optional 
		Sorting
		Pagination
*/

function htmlTable(conf) {
	const config = conf || {table:{id:'htmlTableId'},body:{id:'htmlTableBodyId'}};															// Config object
    let data = [];																		// Data container
	
    // Private helper functions
    function escapeHtml(text) 
	{
        return $('<div>').text(text).html();
    }
	function generateHeaderRow() 
	{
		const header = $("<thead>")
		if (config?.header?.id)header.attr("id", config.header.id);
		const row = $("<tr>");
		
        
        config.columns.forEach(col => 
		{
			let th=$("<th>").text(col.label)
			if(col.header?.class)th.addClass(col.header.class);
            row.append(th);
        });
		header.append(row);

		return header;
    }
	function generateBody()
	{
		const body=$("<tbody>").attr("id",config?.body?.id || "");
		
		if(data.length > 0)
		{
			data.forEach( (dRow,index) => {
				let tRow=generateBodyRow(index,dRow);
				body.append(tRow);
			});

			
		}
		
		return body;
	}
	function generateBodyRow(index,data)
	{
		let tRow=$("<tr>");
		if(config.body?.rows)														// Add any config attributes to row
		{
			Object.keys(config.body.rows).forEach(key => 
			{
				if(typeof config.body.rows[key] === "function")
					tRow.attr(key,config.body.rows[key](index,data));
				else tRow.attr(key,config.body.rows[key]);
			});
		}
		config.columns.forEach( (col,cIndex) => 
		{
			let td = $("<td>");
			let value = "";
			if(col.data?.class)
			{
				if(typeof col.data.class === "function")
					td.addClass(col.data.class(index,data));
				else td.addClass(col.data.class);
			}
			if(col.data?.value)
			{
				if(typeof col.data.value === "function")
					value = col.data.value(index, data);
				else if(typeof data === "object" && !Array.isArray(data))
					value = data[col.data.value];
				else if(Array.isArray(data))
					value=data[cIndex];
			}	
			if(typeof value === "string")
				tRow.append(td.text(value));
			else 
			{
				if (Array.isArray(value))
					value.forEach( v => td.append(v));
				else td.append(value);
				tRow.append(td);
			}
		});
		return tRow;
	}
	/*
		updateExistingRows(tbody)
		Finds all trs in tbody and updates their contents with values from htmlTable.data.  If there are more rows in 
		tbody than data to display it will shed those rows.  If there are less rows in the table than data new rows will
		be appended.
		Input:
			tbody - Jquery dom object - the table's body
	*/
	function  updateExistingRows(tbody) 
	{
		let $rows = tbody.find('tr');

		for(let index=0;index<data.length;index++)
		{
			let dRow=data[index];
			let $row = $rows.eq(index);
			if ($row.length) 
			{
				// Update row attributes
				if (config.body?.rows) 
				{
					Object.keys(config.body.rows).forEach(key => {
						if (typeof config.body.rows[key] === "function")
							$row.attr(key, config.body.rows[key](index, dRow));
						else $row.attr(key, config.body.rows[key]);
					});
				}

				// Update each cell
				$row.find('td').each(function (cIndex) 
				{
					let $td = $(this);
					let col = config.columns[cIndex];
					let value = "";

					// Update class
					if (col.data?.class) 
					{
						$td.attr('class', '');  // clear old classes
						if (typeof col.data.class === "function")
							$td.addClass(col.data.class(index, dRow));
						else $td.addClass(col.data.class);
					}

					// Get value
					if (col.data?.value) 
					{
						if (typeof col.data.value === "function")
							value = col.data.value(index, dRow);
						else if (typeof dRow === "object" && !Array.isArray(dRow))
							value = dRow[col.data.value];
						else if (Array.isArray(dRow))
							value = dRow[cIndex];
					}

					// Set content
					if (typeof value === "string")
						$td.text(value);
					else 
					{
						$td.empty();
						if (Array.isArray(value))
							$td.append(value);
						else $td.append(value);
					}
				});
			} 
			else 
			{
				// More data than rows — append new row
				tbody.append(generateBodyRow(index,dRow));
			}
		}

		// Fewer data than rows — remove extras
		if ($rows.length > data.length)
			$rows.slice(data.length).remove();
	}
    // Public API
    return {
		/*
			setData(ndata)
			Setter function to update the htmlTables data property
			Input:
				ndata - Array - array of arrays or objects that contains the data to be displayed in the table
		*/
		setData(ndata)
		{
			if(!Array.isArray(ndata))
			{
				console.log("htmlTable::setData - Bad paramater, data must be an array of arrays or an array of objects");
				data=[];
			}
			else
				data=ndata;
		},
		/*
			render(container)
			If table does not already exist, render will generate and display the table based on the htmlTable config and 
			data objects.  If the table already exists, it will update the existing rows and trim/append additional rows 
			as needed.
			Input:
				container - string - Id of the element containing the table
		*/
        render(container) 
		{
			let tableSelector='table';
			if(config.table?.id)
				tableSelector+='#'+config.table.id
			
			let table;
			if((table=$(container).find(tableSelector)).length)						// Update just body rows
			{	
				console.log("Table found - UPDATE TBODY");
				updateExistingRows(table.find('tbody'));
			}
			else
			{
				console.log("Table not found - BUIDLING");
				let table=$("<table>");
				if(config.table?.id)
					table.attr("id",config.table.id);
				table.append(generateHeaderRow()).append(generateBody());
				$(container).append(table);
			}
        }
    };
}

window.htmlTable=htmlTable;